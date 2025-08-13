<?php declare(strict_types=1);

namespace Uploadcare\Uploader;

use Psr\Http\Message\ResponseInterface;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\MultipartResponse\MultipartStartResponse;

class Uploader extends AbstractUploader
{
    /**
     * Below this size direct upload is possible, above — multipart upload (100 Mb).
     *
     * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
     */
    public const MULTIPART_UPLOAD_SIZE = 1024 * 1024 * 100;

    /**
     * Multipart upload chunk size (5 Mb).
     *
     * @see https://uploadcare.com/api-refs/upload-api/#tag/Upload/paths/%3Cpresigned-url-x%3E/put
     */
    protected const PART_SIZE = 1024 * 1024 * 5;

    /**
     * @param resource $handle
     */
    public function fromResource($handle, ?string $mimeType = null, ?string $filename = null, string $store = 'auto', array $metadata = []): FileInfoInterface
    {
        try {
            $this->checkResource($handle);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(\sprintf('Wrong parameter at %s: %s', __METHOD__, $e->getMessage()));
        }
        if ($filename === null) {
            $filename = $this->getFileName($handle);
        }

        $this->rewind($handle);
        $arrayKey = 'file';
        if (($fileSize = $this->getSize($handle)) >= self::MULTIPART_UPLOAD_SIZE) {
            $response = $this->uploadByParts($handle, $fileSize, $mimeType, $filename, $store === 'auto' ? null : $store, $metadata);
            $arrayKey = 'uuid';
        } else {
            $response = $this->directUpload($handle, $mimeType, $filename, $store, $metadata);
        }

        return $this->serializeFileResponse($response, $arrayKey);
    }

    /**
     * @param resource $handle
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    private function directUpload($handle, ?string $mimeType = null, ?string $filename = null, string $store = 'auto', array $metadata = []): ResponseInterface
    {
        $parameters = $this->makeMultipartParameters(\array_merge($this->getDefaultParameters(), [
            [
                'name' => 'file',
                'contents' => $handle,
                'filename' => $filename ?: \uuid_create(),
                'headers' => ['Content-Type' => $mimeType],
            ],
            self::UPLOADCARE_STORE_KEY => $store,
        ], $this->makeMetadataParameters($metadata)));

        try {
            $response = $this->sendRequest('POST', 'base/', $parameters);
        } catch (\Throwable $e) {
            throw $this->handleException($e);
        }
        if (\is_resource($handle)) {
            \fclose($handle);
        }

        return $response;
    }

    /**
     * @param resource $handle
     */
    private function uploadByParts($handle, int $fileSize, ?string $mimeType = null, ?string $filename = null, ?string $store = null, array $metadata = []): ResponseInterface
    {
        if ($filename === null) {
            $filename = \uuid_create();
        }
        if ($mimeType === null) {
            $mimeType = 'application/octet-stream';
        }
        if ($store === null) {
            $store = self::UPLOADCARE_DEFAULT_STORE;
        }

        $startData = $this->startUpload($fileSize, $mimeType, $filename, $store, $metadata);
        $this->uploadParts($startData, $handle);

        return $this->finishUpload($startData);
    }

    private function startUpload(int $fileSize, string $mimeType, string $filename, string $store, array $metadata = []): MultipartStartResponse
    {
        $parameters = $this->makeMultipartParameters(\array_merge($this->getDefaultParameters(), [
            'filename' => $filename,
            'size' => $fileSize,
            'content_type' => $mimeType,
            self::UPLOADCARE_STORE_KEY => $store,
        ], $this->makeMetadataParameters($metadata)));

        try {
            $response = $this->sendRequest('POST', 'multipart/start/', $parameters);
            $startData = $this->configuration->getSerializer()
                ->deserialize($response->getBody()->getContents(), MultipartStartResponse::class);
        } catch (\Throwable $e) {
            throw $this->handleException($e);
        }
        if (!$startData instanceof MultipartStartResponse) {
            throw new \RuntimeException(\sprintf('Unable to get %s class from response. Call to support', MultipartStartResponse::class));
        }

        return $startData;
    }

    /**
     * @param resource $handle
     */
    private function uploadParts(MultipartStartResponse $response, $handle): void
    {
        \rewind($handle);
        foreach ($response->getParts() as $signedUrl) {
            $part = \fread($handle, self::PART_SIZE);
            if ($part === false) {
                return;
            }

            try {
                $this->sendRequest('PUT', $signedUrl->getUrl(), ['body' => $part]);
            } catch (\Throwable $e) {
                throw $this->handleException($e);
            }
        }
    }

    private function finishUpload(MultipartStartResponse $response): ResponseInterface
    {
        $data = [
            self::UPLOADCARE_PUB_KEY_KEY => $this->configuration->getPublicKey(),
            'uuid' => $response->getUuid(),
        ];

        try {
            return $this->sendRequest('POST', 'multipart/complete/', $this->makeMultipartParameters($data));
        } catch (\Throwable $e) {
            throw $this->handleException($e);
        }
    }
}
