<?php

namespace Uploadcare\Uploader;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Exception\HttpException;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\MultipartResponse\MultipartStartResponse;

final class Uploader extends AbstractUploader
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
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return FileInfoInterface
     */
    public function fromResource($handle, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface
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
            $response = $this->uploadByParts($handle, $fileSize, $mimeType, $filename, $store === 'auto' ? null : $store);
            $arrayKey = 'uuid';
        } else {
            $response = $this->directUpload($handle, $mimeType, $filename, $store);
        }

        return $this->serializeFileResponse($response, $arrayKey);
    }

    /**
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string|null $store
     *
     * @return ResponseInterface
     */
    private function directUpload($handle, ?string $mimeType = null, ?string $filename = null, string $store = 'auto'): ResponseInterface
    {
        $parameters = $this->makeMultipartParameters(\array_merge($this->getDefaultParameters(), [
            [
                'name' => 'file',
                'contents' => $handle,
                'filename' => $filename ?: \uuid_create(),
                'headers' => ['Content-Type' => $mimeType],
            ],
            self::UPLOADCARE_STORE_KEY => $store,
        ]));

        try {
            $response = $this->sendRequest('POST', 'base/', $parameters);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, ($e instanceof \Exception ? $e : null));
        }
        if (\is_resource($handle)) {
            \fclose($handle);
        }

        return $response;
    }

    /**
     * @param resource    $handle
     * @param int         $fileSize
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string|null $store
     *
     * @return ResponseInterface
     */
    private function uploadByParts($handle, int $fileSize, string $mimeType = null, string $filename = null, string $store = null): ResponseInterface
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

        $startData = $this->startUpload($fileSize, $mimeType, $filename, $store);
        $this->uploadParts($startData, $handle);

        return $this->finishUpload($startData);
    }

    /**
     * @param int    $fileSize
     * @param string $mimeType
     * @param string $filename
     * @param string $store
     *
     * @return MultipartStartResponse
     */
    private function startUpload(int $fileSize, string $mimeType, string $filename, string $store): MultipartStartResponse
    {
        $parameters = $this->makeMultipartParameters(\array_merge($this->getDefaultParameters(), [
            'filename' => $filename,
            'size' => $fileSize,
            'content_type' => $mimeType,
            self::UPLOADCARE_STORE_KEY => $store,
        ]));

        try {
            $response = $this->sendRequest('POST', 'multipart/start/', $parameters);
            $startData = $this->configuration->getSerializer()
                ->deserialize($response->getBody()->getContents(), MultipartStartResponse::class);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, ($e instanceof \Exception ? $e : null));
        }
        if (!$startData instanceof MultipartStartResponse) {
            throw new \RuntimeException(\sprintf('Unable to get %s class from response. Call to support', MultipartStartResponse::class));
        }

        return $startData;
    }

    /**
     * @param MultipartStartResponse $response
     * @param resource               $handle
     *
     * @return void
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
            } catch (GuzzleException $e) {
                throw new HttpException(\sprintf('Upload to %s failed', $signedUrl->getUrl()), 0, ($e instanceof \Exception ? $e : null));
            }
        }
    }

    /**
     * @param MultipartStartResponse $response
     *
     * @return ResponseInterface
     */
    private function finishUpload(MultipartStartResponse $response): ResponseInterface
    {
        $data = [
            self::UPLOADCARE_PUB_KEY_KEY => $this->configuration->getPublicKey(),
            'uuid' => $response->getUuid(),
        ];

        try {
            return $this->sendRequest('POST', 'multipart/complete/', $this->makeMultipartParameters($data));
        } catch (GuzzleException $e) {
            throw new HttpException('Unable to finish multipart-upload request', 0, ($e instanceof \Exception ? $e : null));
        }
    }
}
