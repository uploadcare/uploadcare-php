<?php

namespace Uploadcare;

use Uploadcare\Exceptions\RequestErrorException;
use Uploadcare\Signature\SignatureInterface;

class Uploader
{
    /**
     * Base upload host
     *
     * @var string
     */
    private $host = 'upload.uploadcare.com';

    /**
     * Api instance
     *
     * @var Api
     */
    private $api = null;

    /**
     * @var SignatureInterface|null
     */
    private $secureSignature = null;

    /**
     * @var array|null
     */
    private $requestData = null;

    /**
     * Constructor
     * @param Api $api
     * @param SignatureInterface|null $signature
     */
    public function __construct(Api $api, SignatureInterface $signature = null)
    {
        $this->api = $api;
        $this->secureSignature = $signature;
    }

    /**
     * Return secure signature for signed uploads.
     *
     * @return SignatureInterface|null
     */
    public function getSecureSignature()
    {
        return $this->secureSignature;
    }

    /**
     * Check file status.
     * Return array of json data
     *
     * @param string $token
     * @return object
     * @throws \Exception
     * @throws RequestErrorException
     */
    public function status($token)
    {
        $data = array(
            'token' => $token,
        );

        $this->requestData = $data;

        $ch = $this->__initRequest('from_url/status', $data);
        $this->__setHeaders($ch);
        $data = $this->__runRequest($ch);
        return $data;
    }

    public function __call($method, $arguments)
    {
        if ($method == 'fromUrl') {
            if (count($arguments) == 1) {
                return call_user_func_array(array($this,'fromUrlNew'), $arguments);
            }
            if (count($arguments) == 2) {
                if (is_array($arguments[1])) {
                    return call_user_func_array(array($this,'fromUrlNew'), $arguments);
                } else {
                    return call_user_func_array(array($this,'fromUrlOld'), $arguments);
                }
            } elseif (count($arguments) >= 3) {
                return call_user_func_array(array($this,'fromUrlOld'), $arguments);
            }
        }
    }

    /**
     * Upload file from a URL and get File instance
     *
     * @deprecated 2.0.0 please use fromUrl($url, $options) instead
     * @param string $url A URL of file to be uploaded.
     * @param boolean $check_status Wait till upload is complete
     * @param int $timeout Wait $timeout seconds between status checks
     * @param int $max_attempts Check status no more than $max_attempts times
     * @throws \Exception
     * @throws RequestErrorException
     * @return File|string
     */
    private function fromUrlOld($url, $check_status = true, $timeout = 1, $max_attempts = 5)
    {
        Helper::deprecate('2.0.0', '3.0.0', 'This version of method `fromUrl($url, $check_status, $timeout, $max_attempts)` is deprecated please use `fromUrl($url, $options)` instead');
        return $this->fromUrlNew($url, array(
            'check_status' => $check_status,
            'timeout' => $timeout,
            'max_attempts' => $max_attempts,
        ));
    }

    /**
     * Upload file from a URL and get File instance
     *
     * @param string $url A URL of file to be uploaded.
     * @param array $options Optional dictionary with additional params. Available keys are following:
     *   'store' - can be true, false or 'auto'. This flag indicates should file be stored automatically after upload.
     *   'filename' - should be a string, Sets explicitly file name of uploaded file.
     *   'check_status' - Wait till upload is complete
     *   'timeout' - Wait number of seconds between status checks
     *   'max_attempts' - Check status no more than passed number of times
     * @throws \Exception
     * @throws RequestErrorException
     * @return File|string
     */
    private function fromUrlNew($url, $options = array())
    {
        $default_options = array(
            'store' => 'auto',
            'filename' => null,
            'check_status' => true,
            'timeout' => 1,
            'max_attempts' => 5,
        );
        $params = array_merge($default_options, $options);
        $check_status = $params['check_status'];
        $timeout = $params['timeout'];
        $max_attempts = $params['max_attempts'];

        $requestData = array(
            '_' => time(),
            'source_url' => $url,
            'pub_key' => $this->api->getPublicKey(),
        );

        $requestParameters = array('filename', 'store', 'save_URL_duplicates', 'check_URL_duplicates');
        foreach ($requestParameters as $requestParameter) {
            if (isset($params[$requestParameter]) && !is_null($requestParameter)) {
                $requestData[$requestParameter] = $params[$requestParameter];
            }
        }

        $requestData = $this->getSignedUploadsData($requestData);
        $this->requestData = $requestData;

        $ch = $this->__initRequest('from_url', $requestData);
        $this->__setHeaders($ch);

        $data = $this->__runRequest($ch);
        $token = isset($data->token) ? $data->token : null;

        if ($check_status && $token) {
            $success = false;
            $attempts = 0;
            while (!$success) {
                $data = $this->status($token);
                if ($data->status == 'success') {
                    $success = true;
                }
                if ($data->status == 'error') {
                    throw new \Exception('Upload is not successful: ' . $data->error);
                }
                if ($attempts == $max_attempts && $data->status != 'success') {
                    throw new \Exception('Max attempts reached, upload is not successful');
                }
                sleep($timeout);
                $attempts++;
            }
        } elseif ($token) {
            return $token;
        }
        $uuid = $data->uuid;

        return new File($uuid, $this->api);
    }

    /**
     * Upload file from local path.
     *
     * @param string $path
     * @param string|bool $mime_type
     * @param string $filename
     * @param string|bool $store
     * @return File
     * @throws \Exception
     * @throws RequestErrorException
     */
    public function fromPath(
        $path,
        $mime_type = null,
        $filename = null,
        $store = 'auto'
    ) {
        if (function_exists('curl_file_create')) {
            $f = curl_file_create($path, $mime_type, $filename);
        } else {
            $f = '@' . $path;

            if ($mime_type) {
                $f .= ';type=' . $mime_type;
            }

            if ($filename) {
                $f .= ';filename=' . $filename;
            }
        }

        $data = $this->getSignedUploadsData(array(
          'UPLOADCARE_PUB_KEY' => $this->api->getPublicKey(),
          'UPLOADCARE_STORE' => $store,
          'file' => $f,
        ));
        $this->requestData = $data;

        $ch = $this->__initRequest('base');
        $this->__setRequestType($ch);
        $this->__setData($ch, $data);
        $this->__setHeaders($ch);

        $data = $this->__runRequest($ch);
        $uuid = $data->file;
        return new File($uuid, $this->api);
    }

    /**
     * Upload file from file pointer
     *
     * @param resource $fp
     * @param string $mime_type
     * @param string $filename
     * @param string|bool $store
     * @return File
     * @throws \Exception
     * @throws RequestErrorException
     */
    public function fromResource(
        $fp,
        $mime_type = null,
        $filename = null,
        $store = 'auto'
    ) {
        $tmpfile = tempnam(sys_get_temp_dir(), 'ucr');
        $temp = fopen($tmpfile, 'w');
        while (!feof($fp)) {
            fwrite($temp, fread($fp, 8192));
        }
        fclose($temp);
        fclose($fp);

        return $this->fromPath($tmpfile, $mime_type, $filename, $store);
    }

    /**
     * Upload file from string using mime-type.
     *
     * @param string $content
     * @param string $mime_type
     * @param string $filename
     * @param string|bool $store
     * @return File
     * @throws \Exception
     * @throws RequestErrorException
     */
    public function fromContent($content, $mime_type, $filename = null, $store = 'auto')
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'ucr');
        $temp = fopen($tmpfile, 'w');
        fwrite($temp, $content);
        fclose($temp);

        return $this->fromPath($tmpfile, $mime_type, $filename, $store);
    }

    /**
     * Create group from array of File objects
     *
     * @param array $files
     * @return Group
     * @throws \Exception
     * @throws RequestErrorException
     */
    public function createGroup($files)
    {
        $data = array(
            'pub_key' => $this->api->getPublicKey(),
        );
        /**
         * @var File $file
         */
        foreach ($files as $i => $file) {
            $data["files[$i]"] = $file->getUrl();
        }

        $data = $this->getSignedUploadsData($data);
        $this->requestData = $data;

        $ch = $this->__initRequest('group');
        $this->__setRequestType($ch);
        $this->__setData($ch, $data);
        $this->__setHeaders($ch);

        $resp = $this->__runRequest($ch);
        $group = $this->api->getGroup($resp->id);
        return $group;
    }

    /**
     * Return request data.
     *
     * @return array|null
     */
    private function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * Add secure signature to data for signed uploads.
     *
     * @param array|null $data Data to sign.
     * @return array
     */
    private function getSignedUploadsData($data)
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
     * @param array $data
     * @return resource
     */
    private function __initRequest($type, $data = null)
    {
        $url = sprintf('https://%s/%s/', $this->host, $type);
        if (is_array($data)) {
            $url = sprintf('%s?%s', $url, http_build_query($data));
        }
        $ch = curl_init($url);
        return $ch;
    }

    /**
     * Set request type for curl resource
     *
     * @param resource $ch
     * @return void
     */
    private function __setRequestType($ch)
    {
        curl_setopt($ch, CURLOPT_POST, true);
    }

    /**
     * Set all the headers for request and set return transfer.
     *
     * @param resource $ch. Curl resource.
     * @return void
     */
    private function __setHeaders($ch)
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
    private function __setData($ch, $data = array())
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    /**
     * Run prepared curl request.
     * Throws Exception of not 200 http code
     *
     * @param resource $ch. Curl resource
     * @throws RequestErrorException
     * @return object
     */
    private function __runRequest($ch)
    {
        $data = curl_exec($ch);
        $ch_info = curl_getinfo($ch);
        if ($data === false) {
            throw new RequestErrorException(curl_error($ch), $this->getRequestData());
        } elseif ($ch_info['http_code'] != 200) {
            $message = 'Unexpected HTTP status code ' . $ch_info['http_code'] . '.' . curl_error($ch);
            throw new RequestErrorException($message, $this->getRequestData());
        }
        curl_close($ch);
        return json_decode($data);
    }
}
