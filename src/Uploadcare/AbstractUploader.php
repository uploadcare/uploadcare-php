<?php


namespace Uploadcare;


use Uploadcare\Exceptions\RequestErrorException;
use Uploadcare\Signature\SignatureInterface;

abstract class AbstractUploader
{
    const PIECE_LENGTH = 8192;
    const UPLOADCARE_PUB_KEY_KEY = 'UPLOADCARE_PUB_KEY';
    const UPLOADCARE_STORE_KEY = 'UPLOADCARE_STORE';

    /**
     * Base upload host
     *
     * @var string
     */
    protected $host = 'upload.uploadcare.com';

    /**
     * Api instance
     *
     * @var Api
     */
    protected $api = null;

    /**
     * @var SignatureInterface|null
     */
    protected $secureSignature = null;

    /**
     * @var array
     */
    protected $requestData = array();

    /**
     * Add secure signature to data for signed uploads.
     *
     * @param array|null $data Data to sign.
     * @return array
     */
    protected function getSignedUploadsData($data)
    {
        $secureSignature = $this->secureSignature;
        if (!\is_null($data) && !\is_null($secureSignature)) {
            $data = array_merge($data, array(
                'signature' => $secureSignature->getSignature(),
                'expire' => $secureSignature->getExpire(),
            ));
        }

        return $data;
    }

    /**
     * Init upload request and return curl resource
     *
     * @param $type
     * @param array|null $data
     * @return resource
     */
    protected function initRequest($type, $data = null)
    {
        $url = sprintf('https://%s/%s/', $this->host, $type);
        if (is_array($data)) {
            $url = sprintf('%s?%s', $url, http_build_query($data));
        }
        if (!\is_resource($channel = \curl_init($url))) {
            throw new \RuntimeException('Unable to initialize request');
        }

        return $channel;
    }

    /**
     * Set request type for curl resource
     *
     * @param resource $ch
     * @return void
     */
    protected function setRequestType($ch)
    {
        curl_setopt($ch, CURLOPT_POST, true);
    }

    /**
     * Set all the headers for request and set return transfer.
     *
     * @param resource $ch. Curl resource.
     * @return void
     */
    protected function setHeaders($ch)
    {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: ' . $this->api->getUserAgentHeader(),
        ));
    }

    /**
     * Set data to be posted on request
     *
     * @param resource $ch. Curl resource
     * @param array $data
     * @return void
     */
    protected function setData($ch, $data = array())
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    /**
     * Return request data.
     *
     * @return array|null
     */
    protected function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * Run prepared curl request.
     * Throws Exception of not 200 http code
     *
     * @param resource $ch. Curl resource
     * @param bool     $decode Whether convert response to object
     * @throws RequestErrorException
     * @return object|string
     */
    protected function runRequest($ch, $decode = true)
    {
        $data = curl_exec($ch);
        $ch_info = curl_getinfo($ch);
        if (!\array_key_exists('http_code', $ch_info)) {
            throw new RequestErrorException('Unexpected response: no \'http_code\' key in response', $this->getRequestData());
        }

        if ($data === false) {
            throw new RequestErrorException(curl_error($ch), $this->getRequestData());
        }

        if ($ch_info['http_code'] !== 200) {
            $message = 'Unexpected HTTP status code ' . $ch_info['http_code'] . '.' . curl_error($ch);
            throw new RequestErrorException($message, $this->getRequestData());
        }
        curl_close($ch);

        return $decode ? jsonDecode($data, false) : $data;
    }

    /**
     * @param resource    $fp
     * @param string|null $mime_type
     * @param string      $filename
     * @param string      $store
     * @throws \Exception|RequestErrorException
     * @return File
     */
    public function fromResource($fp, $mime_type = null, $filename = null, $store = 'auto')
    {
        if (!\is_resource($fp)) {
            $message = \sprintf('Expected resource in %s, %s given', __METHOD__, \gettype($fp));
            throw new \RuntimeException($message);
        }

        $tmpfile = tempnam(sys_get_temp_dir(), 'ucr');
        $temp = fopen($tmpfile, 'wb');
        while (!feof($fp)) {
            fwrite($temp, fread($fp, self::PIECE_LENGTH));
        }
        fclose($temp);
        fclose($fp);

        return $this->fromPath($tmpfile, $mime_type, $filename, $store);
    }

    /**
     * Upload file from local path.
     *
     * @param string      $path
     * @param string|null $mime_type
     * @param string      $filename
     * @param string|bool $store
     * @throws \Exception|RequestErrorException
     * @return File
     */
    abstract public function fromPath($path, $mime_type = null, $filename = null, $store = 'auto');
}
