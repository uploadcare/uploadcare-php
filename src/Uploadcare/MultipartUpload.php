<?php

namespace Uploadcare;

use Uploadcare\DataClass\MultipartStartResponse;
use Uploadcare\Exceptions\RequestErrorException;

/**
 * Multipart Upload.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart
 */
class MultipartUpload
{
    /**
     * Chunk size from docs.
     *
     * @see https://uploadcare.com/api-refs/upload-api/#tag/Upload/paths/%3Cpresigned-url-x%3E/put
     */
    const PART_SIZE = 5242880;

    /**
     * @var array request data must be predefined
     */
    private $requestData;

    /**
     * @var string uploadcare url
     */
    private $baseUrl;

    /**
     * @var Uploader
     */
    private $uploader;

    private $boundary;

    /**
     * @param array    $requestData
     * @param string   $baseUrl
     * @param Uploader $uploader
     */
    public function __construct(array $requestData, $baseUrl, Uploader $uploader)
    {
        $this->requestData = $requestData;
        $this->baseUrl = $baseUrl;
        $this->uploader = $uploader;
        $this->boundary = \uniqid('-------------------', false);
    }

    /**
     * @param string      $path     Path to file
     * @param null|string $mimeType
     * @param null|string $filename
     *
     * @throws Exceptions\RequestErrorException|\Exception
     *
     * @return File
     */
    public function uploadByParts($path, $mimeType = null, $filename = null)
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

        $startData = $this->startRequest($this->startRequestData(\filesize($path), $mimeType, $filename));
        $this->uploadParts($startData, $path);
        $finish = $this->finishUpload($startData);

        return new File($startData->getUuid(), $this->uploader->getApi(), (array) $finish);
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
        $ch = $this->initRequest('multipart/start/', array(
            sprintf('Content-Type: multipart/form-data; boundary=%s', $this->boundary),
        ));

        $this->setCurlOptions(array(
            CURLOPT_POST => true,           // REQUIRED on first place!
            CURLOPT_POSTFIELDS => $data,
        ), $ch);

        $result = $this->uploader->runRequest($ch);

        return $raw ? $result : MultipartStartResponse::create($result);
    }

    /**
     * @param int    $size
     * @param string $mimeType
     * @param string $filename
     *
     * @return array
     */
    protected function startRequestData($size, $mimeType, $filename)
    {
        return \array_merge(array(
            'filename' => $filename,
            'size' => $size,
            'content_type' => $mimeType,
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

            $ch = $this->initRequest($signedUrl->getUrl(), array('Content-Type: application/octet-stream'));
            $this->setCurlOptions(array(
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $part,
            ), $ch);

            $this->uploader->runRequest($ch, false);
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
        $ch = $this->initRequest('multipart/complete/', array(
            sprintf('Content-Type: multipart/form-data; boundary=%s', $this->boundary),
        ));
        $this->setCurlOptions(array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
                Uploader::UPLOADCARE_PUB_KEY_KEY => $this->uploader->getApi()->getPublicKey(),
                'uuid' => $response->getUuid()
            ),
        ), $ch);

        return $this->uploader->runRequest($ch);
    }

    /**
     * @param string $path
     * @param array  $headers
     *
     * @return resource
     */
    protected function initRequest($path, array $headers = array())
    {
        $url = \sprintf('https://%s/%s', $this->baseUrl, \ltrim($path, '/'));
        if (\strpos($path, 'http') === 0) {
            $url = $path;
        }

        $channel = \curl_init($url);
        if (!$channel) {
            throw new \RuntimeException('Unable to initialize request');
        }
        $headers = \array_merge(array(
            'User-Agent: '.$this->uploader->getApi()->getUserAgentHeader(),
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
