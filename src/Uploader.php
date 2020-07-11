<?php

namespace Uploadcare;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Exception\HttpException;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\MultipartResponse\MultipartStartResponse;

class Uploader extends AbstractUploader
{
    /**
     * Below this size direct upload is possible, above — multipart upload.
     */
    const MULTIPART_UPLOAD_SIZE = 10485760;

    /**
     * Multipart upload chunk size.
     *
     * @see https://uploadcare.com/api-refs/upload-api/#tag/Upload/paths/%3Cpresigned-url-x%3E/put
     */
    const PART_SIZE = 5242880;

    /**
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string|null $store
     *
     * @return string
     */
    public function fromResource($handle, $mimeType = null, $filename = null, $store = 'auto')
    {
        try {
            $this->checkResource($handle);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(\sprintf('Wrong parameter at %s: %s', __METHOD__, $e->getMessage()));
        }
        $this->rewind($handle);
        if (($fileSize = $this->getSize($handle)) >= self::MULTIPART_UPLOAD_SIZE) {
            return $this->uploadByParts($handle, $fileSize, $mimeType, $filename, $store === 'auto' ? null : $store);
        }

        $response = $this->directUpload($handle, $filename, $store);

        return $this->serializeFileResponse($response);
    }

    /**
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string|null $store
     *
     * @return ResponseInterface
     */
    private function directUpload($handle, $mimeType = null, $filename = null, $store = 'auto')
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
            if (\is_resource($handle)) {
                \fclose($handle);
            }
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
     * @return string
     */
    private function uploadByParts($handle, $fileSize, $mimeType = null, $filename = null, $store = null)
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
    private function startUpload($fileSize, $mimeType, $filename, $store)
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
    private function uploadParts(MultipartStartResponse $response, $handle)
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
     * @return string
     */
    private function finishUpload(MultipartStartResponse $response)
    {
        $data = [
            self::UPLOADCARE_PUB_KEY_KEY => $this->configuration->getPublicKey(),
            'uuid' => $response->getUuid(),
        ];

        try {
            $clientResponse = $this->sendRequest('POST', 'multipart/complete/', $this->makeMultipartParameters($data));
        } catch (GuzzleException $e) {
            throw new HttpException('Unable to finish multipart-upload request', 0, ($e instanceof \Exception ? $e : null));
        }

        return $this->serializeFileResponse($clientResponse, 'uuid');
    }
}
