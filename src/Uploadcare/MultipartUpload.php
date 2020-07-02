<?php

namespace Uploadcare;

use Uploadcare\DataClass\MultipartStartResponse;
use Uploadcare\Exceptions\RequestErrorException;
use Uploadcare\Signature\SignatureInterface;

/**
 * Multipart Upload.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartUpload extends AbstractUploader
{
    /**
     * Chunk size from docs.
     *
     * @see https://uploadcare.com/api-refs/upload-api/#tag/Upload/paths/%3Cpresigned-url-x%3E/put
     */
    const PART_SIZE = 5242880;

    /**
     * Minimal size for multipart upload.
     */
    const MIN_SIZE = 10485760;

    private $boundary;

    /**
     * @param Api                     $api
     * @param SignatureInterface|null $signature
     */
    public function __construct(Api $api, SignatureInterface $signature = null)
    {
        $this->boundary = \uniqid('-------------------', false);
        $this->api = $api;
        $this->secureSignature = $signature;
    }

    /**
     * @inheritDoc
     */
    public function fromPath($path, $mime_type = null, $filename = null, $store = 'auto')
    {
        if (!\is_file($path) || !\is_readable($path)) {
            throw new \RuntimeException(\sprintf('Unable to read file from \'%s\'', $path));
        }
        if (\filesize($path) < self::MIN_SIZE) {
            $uploader = new Uploader($this->api, $this->secureSignature);
            return $uploader->fromPath($path);
        }
        $this->requestData[self::UPLOADCARE_STORE_KEY] = $store;

        $this->requestData = $this->getSignedUploadsData(array(
            self::UPLOADCARE_PUB_KEY_KEY => $this->api->getPublicKey(),
            self::UPLOADCARE_STORE_KEY => $store,
        ));

        return $this->uploadByParts($path, $mime_type, $filename, $store);
    }

    /**
     * @param string      $path     Path to file
     * @param null|string $mimeType
     * @param null|string $filename
     * @param string      $store
     *
     * @throws Exceptions\RequestErrorException|\Exception
     *
     * @return File
     */
    protected function uploadByParts($path, $mimeType = null, $filename = null, $store = 'auto')
    {
        if (!\is_file($path) || !\is_readable($path)) {
            throw new \RuntimeException(\sprintf('Unable to read file from \'%s\'', $path));
        }
        if ($mimeType === null) {
            $mimeType = 'application/octet-stream';
        }
        if ($filename === null) {
            $filename = Uuid::create();
        }

        $this->requestData[self::UPLOADCARE_STORE_KEY] = $store;
        $startData = $this->startRequest($this->extendRequestData(\filesize($path), $mimeType, $filename));
        $this->uploadParts($startData, $path);
        $finish = $this->finishUpload($startData);

        return new File($startData->getUuid(), $this->api, (array) $finish);
    }

    /**
     * @param array $data
     * @param bool  $raw If true, the original response object will be returned.
     *
     * @return MultipartStartResponse|object
     *
     * @throws Exceptions\RequestErrorException
     */
    protected function startRequest(array $data, $raw = false)
    {
        $ch = $this->initMultipartRequest('multipart/start/', array(
            sprintf('Content-Type: multipart/form-data; boundary=%s', $this->boundary),
        ));

        $this->setCurlOptions(array(
            CURLOPT_POST => true,           // REQUIRED on first place!
            CURLOPT_POSTFIELDS => $data,
        ), $ch);

        $result = $this->runRequest($ch);

        return $raw ? $result : MultipartStartResponse::create($result);
    }

    /**
     * @param int    $size
     * @param string $mimeType
     * @param string $filename
     *
     * @return array
     */
    protected function extendRequestData($size, $mimeType, $filename)
    {
        return \array_merge(array(
            'filename' => $filename,
            'size' => $size,
            'content_type' => $mimeType,
            self::UPLOADCARE_PUB_KEY_KEY => $this->api->getPublicKey(),
        ), $this->requestData);
    }

    /**
     * @param MultipartStartResponse $response  Response of start request.
     * @param string                 $path      Path to local file.
     * @throws RequestErrorException
     */
    protected function uploadParts(MultipartStartResponse $response, $path)
    {
        if (!\is_file($path) || !\is_readable($path)) {
            throw new \RuntimeException(\sprintf('Unable to open \'%s\' file for reading', $path));
        }
        $res = \fopen($path, 'rb');

        foreach ($response->getParts() as $signedUrl) {
            $part = \fread($res, self::PART_SIZE);
            if ($part === false) {
                return;
            }

            $ch = $this->initMultipartRequest($signedUrl->getUrl(), array('Content-Type: application/octet-stream'));
            $this->setCurlOptions(array(
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $part,
            ), $ch);

            $this->runRequest($ch, false);
        }
    }

    /**
     * @param MultipartStartResponse $response
     *
     * @return object
     *
     * @throws Exceptions\RequestErrorException
     */
    protected function finishUpload(MultipartStartResponse $response)
    {
        $ch = $this->initMultipartRequest('multipart/complete/', array(
            sprintf('Content-Type: multipart/form-data; boundary=%s', $this->boundary),
        ));
        $this->setCurlOptions(array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
                Uploader::UPLOADCARE_PUB_KEY_KEY => $this->api->getPublicKey(),
                'uuid' => $response->getUuid()
            ),
        ), $ch);

        return $this->runRequest($ch);
    }

    /**
     * @param string $path
     * @param array  $headers
     *
     * @return resource
     */
    protected function initMultipartRequest($path, array $headers = array())
    {
        $url = \sprintf('https://%s/%s', $this->host, \ltrim($path, '/'));
        if (\strpos($path, 'http') === 0) {
            $url = $path;
        }

        $channel = \curl_init($url);
        if (!$channel) {
            throw new \RuntimeException('Unable to initialize request');
        }
        $headers = \array_merge(array(
            'User-Agent: '.$this->api->getUserAgentHeader(),
        ), $headers);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        );

        /**
         * Use CURL_DEEP_DEBUG constant as resource definition for debug purposes.
         * `\define('CURL_DEEP_DEBUG', \fopen('php://stdout', 'w')))` for example
         */
        if (\defined('CURL_DEEP_DEBUG')) {
            $options[CURLOPT_VERBOSE] = true;
            $options[CURLOPT_STDERR] = CURL_DEEP_DEBUG;
        }

        $this->setCurlOptions($options, $channel);

        return $channel;
    }

    /**
     * @param array    $opts
     * @param resource $ch   Curl resource
     */
    private function setCurlOptions(array $opts, &$ch)
    {
        foreach ($opts as $opt => $value) {
            \curl_setopt($ch, $opt, $value);
        }
    }
}
