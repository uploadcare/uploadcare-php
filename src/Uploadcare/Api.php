<?php
namespace Uploadcare;

use Uploadcare\Exceptions\ThrottledRequestException;

$uploadcare_version = '1.5.3';
define('UPLOADCARE_LIB_VERSION', sprintf('%s/%s.%s', $uploadcare_version, PHP_MAJOR_VERSION, PHP_MINOR_VERSION));

class Api
{
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
   * User agent name for HTTP headers
   *
   * @var string
   */
  private $userAgentName = 'PHP Uploadcare Module';

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
   * Uploadcare library version
   *
   * @var string
   */
  public $version = UPLOADCARE_LIB_VERSION;

  /**
   * Uploadcare rest API version
   *
   * @var string
   */
  public $api_version = '0.4';

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
   * Constructor
   *
   * @param string $public_key A public key given by Uploadcare.com
   * @param string $secret_key A private (secret) key given by Uploadcare.com
   * @param string $userAgentName Custom User agent name to report
   * @param string $cdn_host CDN Host
   * @param string $cdn_protocol CDN Protocol
   * @param integer $retry_throttled Retry throttled requests this number of times
   */
  public function __construct($public_key, $secret_key, $userAgentName = null, $cdn_host = null, $cdn_protocol = null, $retry_throttled = null)
  {
    $this->public_key = $public_key;
    $this->secret_key = $secret_key;
    $this->widget = new Widget($this);
    $this->uploader = new Uploader($this);
    if($cdn_host !== null) {
      $this->cdn_host = $cdn_host;
    }
    if($cdn_protocol !== null) {
      $this->cdn_protocol = $cdn_protocol;
    }
    if ($retry_throttled !== null) {
      $this->retry_throttled = $retry_throttled;
    }
    if ($userAgentName !== null) {
      $this->userAgentName = $userAgentName;
    }
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
   * Return CDN URI
   *
   * @return string
   */
  public function getCdnUri()
  {
    return $this->cdn_protocol . '://' . $this->cdn_host;
  }

  /**
   * Convert datetime from string or \DateTime object to ATOM string
   *
   * @param string|\DateTime $datetime
   * @return null|string
   * @throws \Exception
   */
  public static function dateTimeString($datetime)
  {
    if ($datetime === null)
    {
      return null;
    }

    if (is_object($datetime) && !($datetime instanceof \DateTime))
    {
      throw new \Exception('Only \DateTime objects allowed');
    }

    if (is_string($datetime))
    {
      $datetime = new \DateTime($datetime);
    }

    return $datetime->format(\DateTime::ATOM);
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
   * Get portion of files from server respecting filters
   *
   * @param array $options
   * @return array
   */
  public function getFilesChunk($options = array(), $reverse = false)
  {
    $data = $this->__preparedRequest('file_list', 'GET', $options);

    $files_raw = (array)$data->results;
    $result = array();
    foreach ($files_raw as $file_raw) {
      $result[] = new File($file_raw->uuid, $this, $file_raw);
    }

    parse_str(parse_url(!$reverse ? $data->next : $data->previous, PHP_URL_QUERY), $params);

    if ($reverse) {
      $result = array_reverse($result);
    }

    return array(
      'params' => $params,
      'files' => $result,
    );
  }

  /**
   * Return count of files respecting filters
   *
   * @param array $options
   * @return mixed
   */
  public function getFilesCount($options = array())
  {
    $options['limit'] = 1;

    $data = $this->__preparedRequest('file_list', 'GET', $options);

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
   *
   * @param array $options
   * @return FileIterator
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

    return new \Uploadcare\FileIterator($this, $options);
  }

  /**
   * Return an array of groups
   *
   * @param $from string
   * @return array
   */
  public function getGroupList($from = null)
  {
    $params = array();
    if ($from !== null) {
      $params['from'] = $from;
    }
    $data = $this->__preparedRequest('group_list', 'GET', $params);
    $groups = (array)$data->results;
    $result = array();
    foreach ($groups as $group) {
      $result[] = new Group($group->id, $this);
    }
    return $result;
  }

  /**
   * Get group.
   *
   * @param string $uuid_or_url Uploadcare group UUID or CDN URL
   * @return Group
   */
  public function getGroup($uuid_or_url)
  {
    return new Group($uuid_or_url, $this);
  }

  /**
   * Copy file
   *
   * @param string $source CDN URL or file's uuid you need to copy.
   * @param string $target Name of custom storage connected to your project. Uploadcare storage is used if target is absent.
   * @return File|string
   */
  public function copyFile($source, $target = null)
  {
    $data = $this->__preparedRequest('file_copy', 'POST', array(), array('source' => $source, 'target' => $target));
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
   * Run raw request to REST.
   *
   * @param string $method Request method: GET, POST, HEAD, OPTIONS, PUT, etc
   * @param string $path Path to request
   * @param array $data Array of data to send.
   * @param array $headers Additional headers.
   * @return object
   * @throws \Exception
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
    if (!defined('PHPUNIT_UPLOADCARE_TESTSUITE') && ($this->public_key == 'demopublickey' || $this->secret_key == 'demoprivatekey')) {
      trigger_error('You are using the demo account. Please get an Uploadcare account at https://uploadcare.com/accounts/create/', E_USER_WARNING);
    }

    return json_decode($body);
  }

  /**
   * Make request to API.
   * Throws Exception if not http code 200 was returned.
   * If http code 200 it will parse returned data form request as JSON.
   *
   * @param string $type Construct type. Url will be generated using this params. Options: store
   * @param string $request_type Request type. Options: get, post, put, delete.
   * @param array $params Additional parameters for requests as array.
   * @param array $data Data will be posted like json.
   * @param null $retry_throttled
   * @return object
   * @throws \Exception
   * @throws \Uploadcare\Exceptions\ThrottledRequestException
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
   * Convert query array to encoded query string.
   *
   * @param array $queryAr
   * @param string $prefixIfNotEmpty
   * @return string
   */
  private function __getQueryString($queryAr = array(), $prefixIfNotEmpty = '')
  {
    $queryAr = array_filter($queryAr);
    array_walk($queryAr, function(&$val, $key) {
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

      case 'file':
        if (array_key_exists('uuid', $params) == false) {
          throw new \Exception('Please provide "uuid" param for request');
        }
        return sprintf('/files/%s/', $params['uuid']);

      case 'group_list':
        $allowedParams = array('from');
        $queryAr = array();
        foreach ($allowedParams as $paramName) {
          if (isset($params[$paramName])) {
            $queryAr[] = sprintf('%s=%s', $paramName, $params[$paramName]);
          }
        }
        return '/groups/' . ($queryAr ? '?' . join('&', $queryAr) : '');

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
        curl_setopt($ch, CURLOPT_PUT, true);
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
   * Returns full user agent string
   *
   * @return string
   */
  public function getUserAgent()
  {
    return sprintf('%s/%s/%s', $this->userAgentName, $this->version, $this->getPublicKey());
  }

  /**
   * Set all the headers for request and set returntrasfer.
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
    if (count($data)) {
      $content_length = strlen(json_encode($data));
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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
    $content = $data ? json_encode($data) : '';
    $content_md5 = md5(utf8_encode($content));

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
      sprintf('User-Agent: %s', $this->getUserAgent()),
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
   */
  public function getFile($uuid_or_url)
  {
    return new File($uuid_or_url, $this);
  }
}
