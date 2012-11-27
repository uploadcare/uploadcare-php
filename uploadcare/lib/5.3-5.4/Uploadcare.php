<?php
namespace Uploadcare;

define('API_TYPE_STORE', 'store');

define('REQUEST_TYPE_POST', 'post');
define('REQUEST_TYPE_PUT', 'put');
define('REQUEST_TYPE_DELETE', 'delete');
define('REQUEST_TYPE_GET', 'get');

define('UC_PARAM_FILE_ID', 'file_id');

class Api
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
	 * Widget instance.
	 * 
	 * @var string
	 **/
	public $widget = null;
	
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
		$this->widget = new Widget($this);
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
	 * Make request to API.
	 * Throws Exception if not http code 200 was returned.
	 * If http code 200 it will parse returned data form request as JSON.
	 * 
	 * @param string $type Construct type. Url will be generated using this params. Options: store
	 * @param string $request_type Request type. Options: get, post, put, delete.
	 * @param array $params Additional parameters for requests as array.
	 * @throws Exception
	 * @return array
	 **/
	public function request($type, $request_type, $params = array())
	{
		$ch = $this->__initRequest($type, $params);
		$this->__setRequestType($ch, $request_type);
		$this->__setHeaders($ch);
		
		$data = curl_exec($ch);
		$ch_info = curl_getinfo($ch);
		if ($ch_info['http_code'] != 200) {
			throw new \Exception('Request returned unexpected http code '.$ch_info['http_code'].'. '.$data);
		}
		curl_close($ch);
		return json_decode($data);
	}
	
	/**
	 * Inits curl request and rerturn handler
	 * 
	 * @param string $type Construct type. Url will be generated using this params. Options: store
	 * @param array $params Additional parameters for requests as array.
	 * @return resource
	 **/
	private function __initRequest($type, $params = array())
	{
		$url = $this->__getUrl($type, $params);
		return $ch = curl_init($url);		
	}
	
	/**
	 * Return url to send request to.
	 * Throws Exception if wrong type is provided or parameters missing.
	 * 
	 * @param string $type Construct type.
	 * @param array $params Additional parameters for requests as array.
	 * @throws Exception
	 * @return string
	 **/
	private function __getUrl($type, $params = array())
	{
		switch ($type) {
			case API_TYPE_STORE:
				if (array_key_exists(UC_PARAM_FILE_ID, $params) == false)
					throw new \Exception('Please provide "store_id" param for request');					
				return sprintf('https://%s/files/%s/storage/', $this->api_host, $params['file_id']);
				break;
			default:
				throw new \Exception('No api url type is provided for request. Use store, or appropriate constants.');
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
	private function __setRequestType($ch, $type)
	{
		switch ($type) {
			case REQUEST_TYPE_POST:
				curl_setopt($ch, CURLOPT_POST, true);
				break;
			case REQUEST_TYPE_PUT:
				curl_setopt($ch, CURLOPT_PUT, true);
				break;
			case REQUEST_TYPE_DELETE:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');			
				break;
			case REQUEST_TYPE_GET:
				break;
			default:
				throw new \Exception('No request type is provided for request. Use post, put, delete, get or appropriate constants.');
		}		
	}
	
	/**
	 * Set all the headers for request and set returntrasfer.
	 * 
	 * @param resource $ch. Curl resource.
	 * @return void
	 **/
	private function __setHeaders($ch)
	{
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			sprintf('Host: %s', $this->api_host),
			sprintf('Authorization: Uploadcare.Simple %s:%s', $this->public_key, $this->secret_key),
			'Content-Type: application/json',
			'Content-Length: 0',
		));		
	}
	
	/**
	 * Get object of File class by file_id
	 * 
	 * @param string $file_id Uploadcare file_id
	 * @return UploadcareFile
	 **/
	public function getFile($file_id)
	{
		return new File($file_id, $this);		
	}
}

class File
{
	/**
	 * Uploadcare cdn host
	 *
	 * @var string
	 **/
	private $cdn_host = 'ucarecdn.com';

	/**
	 * Uploadcare file id
	 *
	 * @var string
	 **/
	private $file_id = null;

	/**
	 * Uploadcare class instance.
	 *
	 * @var Uploadcare
	 **/
	private $api = null;

	/**
	 * Constructs an object for CDN file with specified ID
	 *
	 * @param string $file_id Uploadcare file_id
	 * @param Uploadcare $api Uploadcare class instance
	 **/
	public function __construct($file_id, Api $api)
	{
		$this->file_id = $file_id;
		$this->api = $api;
	}

	/**
	 * Try to store file.
	 *
	 * @return array
	 **/
	public function store()
	{
		$this->api->request('store', 'post', array('file_id' => $this->file_id));
	}
	
	/**
	 * Get url of original image
	 *
	 * @return string
	 **/
	public function getUrl()
	{
		return sprintf('https://%s/%s/', $this->cdn_host, $this->file_id);
	}	

	/**
	 * Get 400x400 image
	 * Test, will be removed
	 *
	 * @return string
	 **/
	public function getResizedUrl()
	{
		return sprintf('%s-/crop/400x400/center', $this->getUrl());
	}
}

class Widget
{
	/**
	 * Api instance
	 * 
	 * @var Api
	 **/
	private $api = null;
	
	/**
	 * Uploadcare widget version
	 * @var string
	 **/
	private $version = '0.4.2';	
	
	/**
	 * Constructor 
	 * 
	 * @param Api $api
	 **/
	public function __construct(Api $api)
	{
		$this->api = $api;
	}
	
	/**
	 * Returns <script> sections to include Uploadcare widget
	 *
	 * @param string $version Uploadcare version
	 * @return string
	 **/
	public function getInclude($version = null)
	{
		$result = sprintf('<script>UPLOADCARE_PUBLIC_KEY = "%s";</script>', $this->api->getPublicKey());
		$result .= sprintf('<script async="async" src="%s"></script>', $this->getJavascriptUrl($version));
		return $result;
	}	
	
	/**
	 * Echoes <script> sections to include Uploadcare widget
	 **/
	public function printInclude($version = null)
	{
		echo $this->getInclude($version);
	}
	
	/**
	 * Return url for javascript widget.
	 * If no version is provided method will use default(current) version
	 *
	 * @param string $version Version of Uploadcare.com widget
	 * @return string
	 **/
	public function getJavascriptUrl($version = null)
	{
		if (!$version) {
			$version = $this->version;
		}
		return sprintf('https://ucarecdn.com/widget/%s/uploadcare/uploadcare-%s.min.js', $version, $version);
	}	
	
}