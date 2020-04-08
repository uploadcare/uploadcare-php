<?php

namespace Uploadcare;

use Uploadcare\Exceptions\ThrottledRequestException;
use Uploadcare\Signature\SecureSignature;

class Api
{
    /**
     * Uploadcare library version
     */
    const UPLOADCARE_LIBRARY_VERSION = '2.4.0-rc';

    /**
     * Uploadcare public key
     *
     * @var string
     */
    private $public_key = null;

    /**
     * Uploadcare secret key
     *
     * @var string
     */
    private $secret_key = null;

    /**
     * API host for requests
     *
     * @var string
     */
    private $api_host = 'api.uploadcare.com';

    /**
     * @var string secret key for authentication token generation
     */
    public $cdn_secret_token = '';

    /**
     * @var string cdn provider name
     */
    public $cdn_provider = 'akamai';

    /**
     * @var int  access tokens lifetime
     */
    public $lifetime = 0;
    /**
     * Current request method
     *
     * @var string
     */
    private $current_method = null;

    /**
     * Uploadcare CDN host
     *
     * @var string
     */
    public $cdn_host = 'ucarecdn.com';

    /**
     * Uploadcare CDN protocol
     *
     * @var string
     */
    public $cdn_protocol = 'https';

    /**
     * Retry throttled requests this number of times
     *
     * @var int
     */
    private $retry_throttled = 1;

    /**
     * Library name for HTTP headers
     *
     * @var string
     */
    protected $libraryName = 'PHPUploadcare';

    /**
     * User agent name for HTTP headers
     *
     * @var string
     */
    protected $userAgentName = null;

    /**
     * Maximum files number can be processed in file batch operations
     *
     * @var int
     */
    private $batchFilesChunkSize = 100;

    /**
     * Widget instance.
     *
     * @var Widget
     */
    public $widget = null;

    /**
     * Uploader instance
     *
     * @var Uploader
     */
    public $uploader = null;

    /**
     * Uploadcare rest API version
     *
     * @var string
     */
    public $api_version = '0.5';

    /**
     * Default values of filters
     *
     * @var array
     */
    private $defaultFilters = array(
        'file' => array(
            'stored' => null,
            'removed' => false,
        ),
    );

    /**
     * Framework name to report with version.
     *
     * @var string
     */
    protected $framework = '';

    /**
     * Extension name to report with version.
     *
     * @var string
     */
    protected $extension = '';

    /**
     * Constructor
     *
     * @param string $public_key A public key given by Uploadcare.com
     * @param string $secret_key A private (secret) key given by Uploadcare.com
     * @param string $userAgentName Custom User agent name to report
     * @param string $cdn_host CDN Host
     * @param string $cdn_protocol CDN Protocol
     * @param integer $retry_throttled Retry throttled requests this number of times
     * @param int $lifetime Secure signature expire time in seconds for signed uploads.
     * @param string $cdn_secret_token secret key for authentication token generation
     * @param string $cdn_provider
     * @throws \Exception
     */
    public function __construct(
        $public_key,
        $secret_key,
        $userAgentName = null,
        $cdn_host = null,
        $cdn_protocol = null,
        $retry_throttled = null,
        $lifetime = 0,
        $cdn_secret_token = '',
        $cdn_provider = 'akamai'
    ) {
        $this->public_key = $public_key;
        $this->secret_key = $secret_key;
        $this->widget = new Widget($this);
        $this->uploader = new Uploader($this);
        if ($cdn_host !== null) {
            $this->cdn_host = $cdn_host;
        }
        if(empty($this->cdn_host)) {
            throw new \Exception('CDN host must not be empty');
        }
        if ($cdn_protocol !== null) {
            $this->cdn_protocol = $cdn_protocol;
        }
        if ($retry_throttled !== null) {
            $this->retry_throttled = $retry_throttled;
        }
        if ($userAgentName !== null) {
            $this->userAgentName = $userAgentName;
        }

        $signature = null;
        if ($lifetime) {
            $this->lifetime = $lifetime;
            $signature = new SecureSignature($secret_key, $lifetime);
        }

        $this->cdn_secret_token = $cdn_secret_token;
        $this->cdn_provider = $cdn_provider;

        if ($this->cdn_secret_token) {
            if(empty($this->cdn_provider)) {
                throw new \Exception('CDN provider must not be empty');
            }
        }

        $this->widget = new Widget($this, $signature);
        $this->uploader = new Uploader($this, $signature);
    }

    /**
     * Return public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->public_key;
    }

    /**
     * Return lib version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::UPLOADCARE_LIBRARY_VERSION;
    }

    /**
     * Return CDN URI
     *
     * @return string
     */
    public function getCdnUri()
    {
        return $this->cdn_protocol . '://' . $this->cdn_host;
    }

    /**
     * Set framework name with version.
     *
     * @param string $name Framework name (ex: Wordpress, Laravel).
     * @param string $version Framework version (ex: 1.0.1).
     * @return string
     */
    public function setFramework($name, $version)
    {
        return $this->framework = $name.'/'.$version;
    }

    /**
     * Set extension name with version.
     *
     * @param string $name Extension name (ex: PHPUploadcare-Wordpress).
     * @param string $version Extension version (ex: 1.0.1).
     * @return string
     */
    public function setExtension($name, $version)
    {
        return $this->extension = $name.'/'.$version;
    }

    /**
     * Set own User-Agent name for report in HTTP header.
     *
     * @param string $name
     * @return string
     */
    public function setUserAgentName($name)
    {
        return $this->userAgentName = $name;
    }

    /**
     * Get framework name with version.
     *
     * @return string
     */
    public function getFramework()
    {
        return $this->framework;
    }

    /**
     * Get extension name with version.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Get library name.
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Get library name.
     *
     * @return string
     */
    public function getUserAgentName()
    {
        return $this->userAgentName;
    }

    /**
     * Convert datetime from string or \DateTime object to ATOM string
     *
     * @param string|\DateTime $datetime
     * @throws \Exception
     * @return null|string
     */
    public static function dateTimeString($datetime)
    {
        if ($datetime === null) {
            return null;
        }

        if (is_object($datetime) && !($datetime instanceof \DateTime)) {
            throw new \Exception('Only \DateTime objects allowed');
        }

        if (is_string($datetime)) {
            $datetime = new \DateTime($datetime);
        }

        return $datetime->format("Y-m-d\TH:i:s.uP");
    }

    /**
     * Convert boolean to string
     *
     * @param $bool
     * @return string
     */
    public static function booleanString($bool)
    {
        return $bool ? 'true' : 'false';
    }

    /**
     * Get portion of groups from server respecting filters
     *
     * @param array $options
     * @param bool $reverse
     * @return array
     * @throws \Exception
     */
    public function getGroupsChunk($options = array(), $reverse = false)
    {
        $data = $this->__preparedRequest('group_list', 'GET', $options);
        $groups_raw = (array)$data->results;
        $resultArr = array();
        foreach ($groups_raw as $group_raw) {
            $resultArr[] = new Group($group_raw->id, $this);
        }
        return $this->__preparePagedParams($data, $reverse, $resultArr);
    }

    /**
     * Get portion of files from server respecting filters
     *
     * @param array $options
     * @param bool $reverse
     * @return array
     * @throws \Exception
     */
    public function getFilesChunk($options = array(), $reverse = false)
    {
        $data = $this->__preparedRequest('file_list', 'GET', $options);
        $files_raw = (array)$data->results;
        $resultArr = array();
        foreach ($files_raw as $file_raw) {
            $resultArr[] = new File($file_raw->uuid, $this, $file_raw);
        }
        return $this->__preparePagedParams($data, $reverse, $resultArr);
    }


    /**
     * Return count of files respecting filters
     *
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function getFilesCount($options = array())
    {
        $options['limit'] = 1;

        $data = $this->__preparedRequest('file_list', 'GET', $options);

        return $data->total;
    }

    /**
     * Return count of groups respecting filters
     *
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function getGroupsCount($options = array())
    {
        $options['limit'] = 1;

        $data = $this->__preparedRequest('group_list', 'GET', $options);

        return $data->total;
    }

    /**
     * Return an iterator of File objects to work with.
     *
     * This class provides iteration over all uploaded files. You can specify:
     *   - $options['from'] - a DateTime object or string from which objects will be iterated;
     *   - $options['to'] - a DateTime object or string to which objects will be iterated;
     *   - $options['limit'] - a total number of objects to be iterated;
     *     If not specified, all available objects are iterated;
     *   - $options['request_limit'] - a number of objects to be downloaded per request.
     *   - $options['stored'] - True to include only stored files,
     *     False to exclude, Null is default, will not exclude anything;
     *   - $options['removed'] - True to include only removed files,
     *     False to exclude, Null will not exclude anything.
     *     The default is False.
     *   - $options['reversed'] - If True then result list will be reversed
     *
     * @param array $options
     * @return FileIterator
     * @throws \Exception
     */
    public function getFileList($options = array())
    {
        $options = array_replace(array(
            'from' => null,
            'to' => null,
            'limit' => null,
            'request_limit' => null,
            'stored' => $this->defaultFilters['file']['stored'],
            'removed' => $this->defaultFilters['file']['removed'],
            'reversed' => false,
        ), $options);

        if (!empty($options['from']) && !empty($options['to'])) {
            throw new \Exception('Only one of "from" and "to" arguments is allowed');
        }

        $options['from'] = self::dateTimeString($options['from']);
        $options['to'] = self::dateTimeString($options['to']);

        foreach ($this->defaultFilters['file'] as $k => $v) {
            if (!is_null($options[$k])) {
                $options[$k] = self::booleanString($options[$k]);
            }
        }

        return new FileIterator($this, $options);
    }

    /**
     * Return an iterator of Group objects to work with.
     *
     * This class provides iteration over all uploaded file groups. You can specify:
     *   - $options['from'] - a DateTime object or string from which objects will be iterated;
     *   - $options['to'] - a DateTime object or string to which objects will be iterated;
     *   - $options['limit'] - a total number of objects to be iterated;
     *     If not specified, all available objects are iterated;
     *   - $options['request_limit'] - a number of objects to be downloaded per request.
     *   - $options['reversed'] - If True then result list will be reversed
     *
     * @param array $options
     * @return GroupIterator
     * @throws \Exception
     */
    public function getGroupList($options = array())
    {
        $options = array_replace(array(
            'from' => null,
            'to' => null,
            'limit' => null,
            'request_limit' => null,
            'reversed' => false,
        ), $options);

        if (!empty($options['from']) && !empty($options['to'])) {
            throw new \Exception('Only one of "from" and "to" arguments is allowed');
        }

        $options['from'] = self::dateTimeString($options['from']);
        $options['to'] = self::dateTimeString($options['to']);

        return new GroupIterator($this, $options);
    }

    /**
     * Get group.
     *
     * @param string $uuid_or_url Uploadcare group UUID or CDN URL
     * @return Group
     * @throws \Exception
     */
    public function getGroup($uuid_or_url)
    {
        return new Group($uuid_or_url, $this);
    }

    /**
     * Copy file
     *
     * @deprecated 2.0.0 Use createLocalCopy() or createRemoteCopy() instead.
     * @param string $source CDN URL or file's uuid you need to copy.
     * @param string $target Name of custom storage connected to your project. Uploadcare storage is used if target is absent.
     * @return File|string
     * @throws \Exception
     */
    public function copyFile($source, $target = null)
    {
        Helper::deprecate('2.0.0', '3.0.0', 'Use createLocalCopy() or createRemoteCopy() instead');
        if (!$target) {
            return $this->createLocalCopy($source);
        } else {
            return $this->createRemoteCopy($source, $target);
        }
    }

    /**
     * Copy file to the Uploadcare storage
     *
     * @param string $source CDN URL or file's uuid you need to copy.
     * @param boolean $store MUST be either true or false. true to store files while copying. If stored, files won’t be automatically deleted within 24 hours after copying. false * to not store files, default.
     * @return File|string
     * @throws \Exception
     */
    public function createLocalCopy($source, $store = true)
    {
        $data = $this->__preparedRequest('file_copy', 'POST', array(), array('source' => $source, 'store' => $store));
        if (array_key_exists('result', (array)$data) == true) {
            if ($data->type == 'file') {
                return new File((string)$data->result->uuid, $this);
            } else {
                return (string)$data->result;
            }
        } else {
            return (string)$data->detail;
        }
    }

    /**
     * Copy file to the external storage
     *
     * @param string $source CDN URL or file's uuid you need to copy.
     * @param string $target Name of custom storage connected to your project. Uploadcare storage is used if target is absent.
     * @param boolean $make_public (Optional) MUST be either true or false. true to make copied files available via public links. false to reverse the behavior.
     * @param string $pattern (Optional) Applies to custom storage usage scenario only. The parameter is used to specify file names Uploadcare passes to custom storages. In case parameter is omitted, custom storage pattern is used.
     *
     * Allowed values:
     *
     * ${default} = ${uuid}/${auto_filename}
     * ${auto_filename} = ${filename} ${effects} ${ext}
     * ${effects} = CDN operations put into a URL
     * ${filename} = original filename, no extension
     * ${uuid} = file UUID
     * ${ext} = file extension, leading dot, e.g. .jpg
     *
     * @return string
     * @throws \Exception
     */
    public function createRemoteCopy($source, $target, $make_public = true, $pattern = null)
    {
        if (!$target) {
            throw new \Exception('$target parameter should not be empty.');
        }
        $paramArr = array('source' => $source, 'target' => $target, 'make_public' => $make_public);
        if ($pattern) {
            $paramArr['pattern'] = $pattern;
        }
        $data = $this->__preparedRequest('file_copy', 'POST', array(), $paramArr);
        if (array_key_exists('result', (array)$data) == true) {
            return (string)$data->result;
        } else {
            return (string)$data->detail;
        }
    }


    /**
     * Store multiple files
     *
     * @param array $filesUuidArr uploaded file's uuid array you need to store.
     * @return array with stored files and problems if any
     * @throws \Exception
     */
    public function storeMultipleFiles($filesUuidArr)
    {
        return $this->__batchProcessFiles($filesUuidArr, 'PUT');
    }

    /**
     * Delete multiple files
     *
     * @param array $filesUuidArr uploaded or stored file's uuid array you need to delete.
     * @return array with deleted files and problems if any
     * @throws \Exception
     */
    public function deleteMultipleFiles($filesUuidArr)
    {
        return $this->__batchProcessFiles($filesUuidArr, 'DELETE');
    }

    /**
     * Process multiple files with chunk support
     *
     * @param array $filesUuidArr uploaded file's uuid array you need to process.
     * @param string $request_type request type, could be PUT or DELETE .
     * @return array with processed files and problems if any
     * @throws \Exception
     */
    public function __batchProcessFiles($filesUuidArr, $request_type)
    {
        $filesChunkedArr = array_chunk($filesUuidArr, $this->batchFilesChunkSize);
        $filesArr = array();
        $problemsArr = array();
        $lastStatus = '';
        foreach ($filesChunkedArr as $chunk) {
            $res = $this->__batchProcessFilesChunk($chunk, $request_type);
            $lastStatus = $res['status'];
            if ($lastStatus == "ok") {
                $problemsObj = $res['problems'];
                if (count(get_object_vars($problemsObj)) > 0) {
                    $problemsArr [] = $problemsObj;
                }
                $filesArr = array_merge($filesArr, $res['files']);
            } else {
                throw new \Exception('Error process multiple files', $res);
            }
        }
        return array(
            'status' => $lastStatus,
            'files' => $filesArr,
            'problems' => $problemsArr,
        );
    }

    /**
     * Run request to process multiple files
     *
     * @param array $filesUuidArr uploaded file's uuid array you need to process.
     * @param string $request_type request type, could be PUT or DELETE .
     * @return array with processed files and problems if any
     * @throws \Exception
     */
    public function __batchProcessFilesChunk($filesUuidArr, $request_type)
    {
        if (count($filesUuidArr) > $this->batchFilesChunkSize) {
            throw new \Exception('Files number should not exceed '.$this->batchFilesChunkSize.' items per request.');
        }
        $data = $this->__preparedRequest('files_storage', $request_type, array(), $filesUuidArr);
        $files_raw = (array)$data->result;
        $result = array();
        foreach ($files_raw as $file_raw) {
            $result[] = new File($file_raw->uuid, $this, $file_raw);
        }
        return array(
            'status' => (string)$data->status,
            'files' => $result,
            'problems' => $data->problems,
        );
    }

    /**
     * Run raw request to REST.
     *
     * @param string $method Request method: GET, POST, HEAD, OPTIONS, PUT, etc
     * @param string $path Path to request
     * @param array $data Array of data to send.
     * @param array $headers Additional headers.
     * @throws \Exception
     * @return object
     */
    public function request($method, $path, $data = array(), $headers = array())
    {
        $ch = curl_init(sprintf('https://%s%s', $this->api_host, $path));
        $this->__setRequestType($ch, $method);
        $this->__setHeaders($ch, $headers, $data);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }
        $ch_info = curl_getinfo($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $error = false;

        if ($method == 'DELETE') {
            if ($ch_info['http_code'] != 302 && $ch_info['http_code'] != 200) {
                $error = true;
            }
        } else {
            if (!(($ch_info['http_code'] >= 200) && ($ch_info['http_code'] < 300))) {
                $error = true;
            }
        }

        if ($ch_info['http_code'] == 429) {
            $exception = new ThrottledRequestException();
            $response_headers = Helper::parseHttpHeaders($header);
            $exception->setResponseHeaders($response_headers);
            throw $exception;
        }

        if ($error) {
            $errorInfo = array_filter(array(curl_error($ch), $body));

            throw new \Exception('Request returned unexpected http code '. $ch_info['http_code'] . '. ' . join(', ', $errorInfo));
        }

        curl_close($ch);
        if (!defined('PHPUNIT_UPLOADCARE_TESTSUITE') && ($this->public_key == 'demopublic_key' || $this->secret_key == 'demoprivatekey')) {
            trigger_error('You are using the demo account. Please get an Uploadcare account at https://uploadcare.com/accounts/create/', E_USER_WARNING);
        }

        return json_decode($body);
    }

    /**
     * Make request to API.
     * Throws Exception if not http code 200 was returned.
     * If http code 200 it will parse returned data form request as JSON.
     *
     * @param string $type Construct type. URL will be generated using this params. Options: store
     * @param string $request_type Request type. Options: get, post, put, delete.
     * @param array $params Additional parameters for requests as array.
     * @param array $data Data will be posted like json.
     * @param null $retry_throttled
     * @throws \Exception
     * @throws \Uploadcare\Exceptions\ThrottledRequestException
     * @return object|null
     */
    public function __preparedRequest($type, $request_type = 'GET', $params = array(), $data = array(), $retry_throttled = null)
    {
        $retry_throttled = $retry_throttled ?: $this->retry_throttled;
        $path = $this->__getPath($type, $params);

        while (true) {
            try {
                return $this->request($request_type, $path, $data);
            } catch (ThrottledRequestException $exception) {
                if ($retry_throttled > 0) {
                    sleep($exception->getTimeout());
                    $retry_throttled--;
                    continue;
                } else {
                    throw $exception;
                }
            }
        }

        return null;
    }

    /**
     * Prepares paged params array from chunk request result.
     *
     * @param object $data
     * @param boolean $reverse
     * @param array $resultArr
     * @return array
     */
    private function __preparePagedParams($data, $reverse, $resultArr)
    {
        $nextParamsArr = parse_url($data->next);
        $prevParamsArr = parse_url($data->previous);
        $nextParamsArr = array_replace(array('query' => null), $nextParamsArr);
        $prevParamsArr = array_replace(array('query' => null), $prevParamsArr);

        parse_str(parse_url(!$reverse ? $data->next : $data->previous, PHP_URL_QUERY), $params);

        if ($reverse) {
            $resultArr = array_reverse($resultArr);
        }

        return array(
            'nextParams' => $reverse ? $prevParamsArr : $nextParamsArr,
            'prevParams' => !$reverse ? $prevParamsArr : $nextParamsArr,
            'params' => $params,
            'data' => $resultArr,
        );
    }

    /**
     * Convert query array to encoded query string.
     *
     * @param array $queryAr
     * @param string $prefixIfNotEmpty
     * @return string
     */
    private function __getQueryString($queryAr = array(), $prefixIfNotEmpty = '')
    {
        $queryAr = array_filter($queryAr);
        array_walk($queryAr, function (&$val, $key) {
            $val = urlencode($key) . '=' . urlencode($val);
        });

        return $queryAr ? $prefixIfNotEmpty . join('&', $queryAr) : '';
    }

    /**
     * Return path to send request to.
     * Throws Exception if wrong type is provided or parameters missing.
     *
     * @param string $type Construct type.
     * @param array $params Additional parameters for requests as array.
     * @throws \Exception
     * @return string
     */
    private function __getPath($type, $params = array())
    {
        switch ($type) {
            case 'root':
                return '/';
            case 'account':
                return '/account/';
            case 'file_list':
                return '/files/' . $this->__getQueryString($params, '?');
            case 'file_storage':
                if (array_key_exists('uuid', $params) == false) {
                    throw new \Exception('Please provide "uuid" param for request');
                }
                return sprintf('/files/%s/storage/', $params['uuid']);
            case 'file_copy':
                return '/files/';
            case 'files_storage':
                return '/files/storage/';
            case 'file':
                if (array_key_exists('uuid', $params) == false) {
                    throw new \Exception('Please provide "uuid" param for request');
                }
                return sprintf('/files/%s/', $params['uuid']);
            case 'group_list':
                return '/groups/' . $this->__getQueryString($params, '?');
            case 'group':
                if (array_key_exists('uuid', $params) == false) {
                    throw new \Exception('Please provide "uuid" param for request');
                }
                return sprintf('/groups/%s/', $params['uuid']);
            case 'group_storage':
                if (array_key_exists('uuid', $params) == false) {
                    throw new \Exception('Please provide "uuid" param for request');
                }
                return sprintf('/groups/%s/storage/', $params['uuid']);
            default:
                throw new \Exception('No api url type is provided for request "' . $type . '". Use store, or appropriate constants.');
        }
    }

    /**
     * Set request type.
     * If request type is wrong an Exception will be thrown.
     *
     * @param resource $ch. Curl resource.
     * @param string $type Request type. Options: get, post, put, delete.
     * @throws \Exception
     * @return void
     */
    private function __setRequestType($ch, $type = 'GET')
    {
        $this->current_method = strtoupper($type);

        switch ($type) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'HEAD':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
                curl_setopt($ch, CURLOPT_NOBODY, true);
                break;
            case 'OPTIONS':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
                break;
            default:
                throw new \Exception('No request type is provided for request. Use post, put, delete, get or appropriate constants.');
        }
    }

    /**
     * Returns full user agent string.
     * @deprecated 2.2.1 Use getUserAgentHeader() instead.
     * @return string
     */
    public function getUserAgent() {
        Helper::deprecate('2.0.0', '3.0.0', 'Use getUserAgentHeader() instead');
        return $this->getUserAgentHeader();
    }

    /**
     * Returns full user agent string.
     *
     * @return string
     */
    public function getUserAgentHeader()
    {
        // If has own User-Agent
        $userAgentName = $this->getUserAgentName();
        if ($userAgentName) {
            return $userAgentName;
        }

        $userAgent = sprintf('%s/%s/%s (PHP/%s.%s.%s', $this->getLibraryName(), $this->getVersion(), $this->getPublicKey(), PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION);

        $framework = $this->getFramework();
        if ($framework) {
            $userAgent .= '; '.$framework;
        }

        $extension = $this->getExtension();
        if ($extension) {
            $userAgent .= '; '.$extension;
        }

        $userAgent .= ')';

        return $userAgent;
    }

    /**
     * Set all the headers for request and set return transfer.
     *
     * @param resource $ch. Curl resource.
     * @param array $add_headers additional headers.
     * @param array $data Data array.
     * @throws \Exception
     * @return void
     */
    private function __setHeaders($ch, $add_headers = array(), $data = array())
    {
        $content_length = 0;
        $rawContent = '';
        if (count($data)) {
            $rawContent = utf8_encode(json_encode($data));
            $content_length = strlen($rawContent);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $rawContent);
        }

        // path
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $url_parts = parse_url($url);

        if ($url_parts === false) {
            throw new \Exception('Incorrect URL ' . $url);
        }

        $path = $url_parts['path'] . (!empty($url_parts['query']) ? '?' . $url_parts['query'] : '');

        // content
        $content_type = 'application/json';
        $content_md5 = md5($rawContent);

        // date
        $date = gmdate('D, d M Y H:i:s \G\M\T');

        // sign string
        $sign_string = join("\n", array(
            $this->current_method,
            $content_md5,
            $content_type,
            $date,
            $path,
        ));
        $sign_string_as_bytes = utf8_encode($sign_string);

        $secret_as_bytes = utf8_encode($this->secret_key);

        $sign = hash_hmac('sha1', $sign_string_as_bytes, $secret_as_bytes);

        $headers = array(
            sprintf('Host: %s', $this->api_host),
            sprintf('Authorization: Uploadcare %s:%s', $this->public_key, $sign),
            sprintf('Date: %s', $date),
            sprintf('Content-Type: %s', $content_type),
            sprintf('Content-Length: %d', $content_length),
            sprintf('Accept: application/vnd.uploadcare-v%s+json', $this->api_version),
            sprintf('User-Agent: %s', $this->getUserAgentHeader()),
        ) + $add_headers;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }

    /**
     * Get object of File class by id
     *
     * @param string $uuid_or_url Uploadcare file UUID or CDN URL
     * @return File
     * @throws \Exception
     */
    public function getFile($uuid_or_url)
    {
        return new File($uuid_or_url, $this);
    }
}
