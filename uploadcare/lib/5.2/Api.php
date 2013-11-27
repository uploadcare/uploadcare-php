<?php
/**
 * @file
 * 
 * Uploadcare_Api
 */

class Uploadcare_Api
{
  /**
   * Uploadcare public key
   *
   * @var string
   **/
  private $public_key = null;

  /**
   * Uploadcare secret key
   *
   * @var string
   **/
  private $secret_key = null;

  /**
   * API host for requests
   *
   * @var string
   **/
  private $api_host = 'api.uploadcare.com';

  /**
   * Uploadcare_Widget instance.
   *
   * @var Uploadcare_Widget
   **/
  public $widget = null;

  /**
   * Uploadcare_Uploader instance
   *
   * @var Uploadcare_Uploader
   **/
  public $uploader = null;

  /**
   * Uploadcare library version
   *
   * @var string
   **/
  public $version = '1.0.4/5.2';

  /**
   * Uploadcare rest API version
   *
   * @var string
   */
  public $api_version = '0.3';

  /**
   * Constructor
   *
   * @param string $public_key A public key given by Uploadcare.com
   * @param string $secret_key A private (secret) key given by Uploadcare.com
   * @return void
   **/
  public function __construct($public_key, $secret_key)
  {
    $this->public_key = $public_key;
    $this->secret_key = $secret_key;
    $this->widget = new Uploadcare_Widget($this);
    $this->uploader = new Uploadcare_Uploader($this);
  }

  /**
   * Returns public key
   *
   * @return string
   **/
  public function getPublicKey()
  {
    return $this->public_key;
  }

  /**
   * Return an array of File objects to work with.
   *
   * @param integer $page Page to be shown.
   * @return array
   **/
  public function getFileList($page = 1)
  {
    $data = $this->__preparedRequest(API_TYPE_FILES, REQUEST_TYPE_GET, array('page' => $page));
    $files_raw = (array)$data->results;
    $result = array();
    foreach ($files_raw as $file_raw) {
      $result[] = new Uploadcare_File($file_raw->file_id, $this);
    }
    return $result;
  }

  /**
   * Return an array of groups
   *
   * @param $from string
   * @return array
   */
  public function getGroupList($from = null)
  {
    $data = $this->__preparedRequest(API_TYPE_GROUPS, REQUEST_TYPE_GET, array('from' => $from));
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
   * @param $group_id string Group ID.
   */
  public function getGroup($group_id)
  {
    return new Group($group_id, $this);
  }  
  
  /**
   * Get info about pagination.
   *
   * @param integer $page
   * @return array
   **/
  public function getFilePaginationInfo($page = 1)
  {
    $data = (array)$this->__preparedRequest(API_TYPE_FILES, REQUEST_TYPE_GET, array('page' => $page));
    unset($data['results']);
    return $data;
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
    $data = $this->__preparedRequest(API_TYPE_FILES, REQUEST_TYPE_POST, array(), array('source' => $source, 'target' => $target));
    if (key_exists('result', (array)$data) == true) {
      return new Uploadcare_File((string)$data->result->uuid, $this);
    } else {
      return (string)$data->detail;
    }
  }

  /**
   * Run raw request to REST.
   *
   * @param string $method Request method: GET, POST, HEAD, OPTIONS, PUT, etc
   * @param string $path Path to request
   * @param string $data Array of data to send.
   * @param string $headers Additonal headers.
   * @return array
   **/
  public function request($method, $path, $data = array(), $headers = array())
  {
    $ch = curl_init(sprintf('https://%s%s', $this->api_host, $path));
    $this->__setRequestType($ch, $method);
    $this->__setHeaders($ch, $headers, $data);

    $data = curl_exec($ch);
    if ($data === false) {
      throw new Exception(curl_error($ch));
    }
    $ch_info = curl_getinfo($ch);
    if ($method == REQUEST_TYPE_DELETE) {
      if ($ch_info['http_code'] != 302) {
        throw new Exception('Request returned unexpected http code '.$ch_info['http_code'].'. '.$data);
      }
    } else {
      if (!(($ch_info['http_code'] >= 200)&&($ch_info['http_code'] < 300))) {
        throw new Exception('Request returned unexpected http code '.$ch_info['http_code'].'. '.$data);
      }
    }
    curl_close($ch);
    if ($this->public_key == 'demopublickey' || $this->secret_key == 'demoprivatekey') {
      trigger_error('You are using the demo account. Please get an Uploadcare account at https://uploadcare.com/accounts/create/', E_USER_WARNING);
    }
    return json_decode($data);
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
   * @throws Exception
   * @return array
   **/
  public function __preparedRequest($type, $request_type = REQUEST_TYPE_GET, $params = array(), $data = array())
  {
    $path = $this->__getPath($type, $params);
    return $this->request($request_type, $path, $data);
  }

  /**
   * Return path to send request to.
   * Throws Exception if wrong type is provided or parameters missing.
   *
   * @param string $type Construct type.
   * @param array $params Additional parameters for requests as array.
   * @throws Exception
   * @return string
   **/
  private function __getPath($type, $params = array())
  {
      switch ($type) {
      case API_TYPE_RAW:
        return '/';
      case API_TYPE_ACCOUNT:
        return '/account/';
      case API_TYPE_FILES:
        return sprintf('/files/?page=%s', $params['page']);
      case API_TYPE_STORE:
        if (array_key_exists(UC_PARAM_FILE_ID, $params) == false) {
          throw new Exception('Please provide "store_id" param for request');
        }
        return sprintf('/files/%s/storage/', $params['file_id']);
      case API_TYPE_FILE:
        if (array_key_exists(UC_PARAM_FILE_ID, $params) == false) {
          throw new Exception('Please provide "store_id" param for request');
        }
        return sprintf('/files/%s/', $params['file_id']);
      case API_TYPE_GROUPS:
        return sprintf('/groups/?from=%s', $params['from']);
      case API_TYPE_GROUP:
        return sprintf('/groups/%s/', $params['group_id']);
      case API_TYPE_STORE:
          if (array_key_exists(UC_PARAM_GROUP_ID, $params) == false) {
            throw new Exception('Please provide "group_id" param for request');
          }
          return sprintf('/groups/%s/storage/', $params['group_id']);  
      default:
        throw new Exception('No api url type is provided for request. Use store, or appropriate constants.');
    }
  }

  /**
   * Set request type.
   * If request type is wrong an Exception will be thrown.
   *
   * @param resource $ch. Curl resource.
   * @param string $type Request type. Options: get, post, put, delete.
   * @throws Exception
   * @return void
   **/
  private function __setRequestType($ch, $type = REQUEST_TYPE_GET)
  {
    switch ($type) {
      case REQUEST_TYPE_GET:
      case 'GET':
        break;
      case REQUEST_TYPE_POST:
      case 'POST':
        curl_setopt($ch, CURLOPT_POST, true);
        break;
      case REQUEST_TYPE_PUT:
      case 'PUT':
        curl_setopt($ch, CURLOPT_PUT, true);
        break;
      case REQUEST_TYPE_DELETE:
      case 'DELETE':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        break;
      case REQUEST_TYPE_HEAD:
      case 'HEAD':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
        break;
      case REQUEST_TYPE_OPTIONS:
      case 'OPTIONS':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        break;
      default:
        throw new Exception('No request type is provided for request. Use post, put, delete, get or appropriate constants.');
    }
  }

  /**
   * Set all the headers for request and set returntrasfer.
   *
   * @param resource $ch. Curl resource.
   * @param array $headers additional headers.
   * @param array $data Data array.
   * @return void
   **/
  private function __setHeaders($ch, $add_headers = array(), $data = array())
  {
    $content_length = 0;
    if (count($data)) {
      $content_length = strlen(json_encode($data));
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $headers = array(
        sprintf('Host: %s', $this->api_host),
        sprintf('Authorization: Uploadcare.Simple %s:%s', $this->public_key, $this->secret_key),
        'Content-Type: application/json',
        'Content-Length: '.$content_length,
        'User-Agent: PHP Uploadcare Module '.$this->version,
        'Accept: application/vnd.uploadcare-v'.$this->api_version.'+json',
        sprintf('Date: %s', date('Y-m-d H:i:s')),
    ) + $add_headers;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }

  /**
   * Get object of Uploadcare_File class by file_id
   *
   * @param string $file_id Uploadcare file_id
   * @return Uploadcare_File
   **/
  public function getFile($file_id)
  {
    return new Uploadcare_File($file_id, $this);
  }
}
