<?php
namespace Uploadcare;

define('API_TYPE_STORE', 'store');
define('API_TYPE_RAW', 'raw');
define('API_TYPE_ACCOUNT', 'account');
define('API_TYPE_FILES', 'files');

define('REQUEST_TYPE_POST', 'post');
define('REQUEST_TYPE_PUT', 'put');
define('REQUEST_TYPE_DELETE', 'delete');
define('REQUEST_TYPE_GET', 'get');
define('REQUEST_TYPE_HEAD', 'head');
define('REQUEST_TYPE_OPTIONS', 'options');

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
	 * Return an array of File objects to work with.
	 * 
	 * @return array
	 **/
	public function getFileList()
	{
		$data = $this->request(API_TYPE_FILES);
		$files_raw = (array)$data->results;
		$result = array();
		foreach ($files_raw as $file_raw) {
			$result[] = new File($file_raw->file_id, $this);
		}
		return $result;
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
	public function request($type, $request_type = REQUEST_TYPE_GET, $params = array())
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
			case API_TYPE_RAW:
				return sprintf('https://%s/', $this->api_host);
			case API_TYPE_ACCOUNT:
				return sprintf('http://%s/account/', $this->api_host);
			case API_TYPE_FILES:
				return sprintf('http://%s/files/', $this->api_host);
			case API_TYPE_STORE:
				if (array_key_exists(UC_PARAM_FILE_ID, $params) == false) {
					throw new \Exception('Please provide "store_id" param for request');			
				}		
				return sprintf('https://%s/files/%s/storage/', $this->api_host, $params['file_id']);
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
	private function __setRequestType($ch, $type = REQUEST_TYPE_GET)
	{
		switch ($type) {
			case REQUEST_TYPE_GET:
				break;			
			case REQUEST_TYPE_POST:
				curl_setopt($ch, CURLOPT_POST, true);
				break;
			case REQUEST_TYPE_PUT:
				curl_setopt($ch, CURLOPT_PUT, true);
				break;
			case REQUEST_TYPE_DELETE:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');			
				break;
			case REQUEST_TYPE_HEAD:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
				break;
			case REQUEST_TYPE_OPTIONS:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
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
			'User-Agent: PHP Uploadcare Module',
			sprintf('Date: %s', date('Y-m-d H:i:s')),
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
	 * Operations and params for operations: crop, resize, scale_crop, effect.
	 * 
	 * @var array
	 */
	private $operations = array();

	/**
	 * Uploadcare class instance.
	 *
	 * @var Uploadcare
	 **/
	private $api = null;

	/**
	 * Operations list
	 **/
	private $operation_list = array('crop', 'resize', 'scale_crop', 'effect');
	
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
	 * Return file_id for this file
	 * 
	 * @return string
	 **/
	public function getFileId()
	{
		return $this->file_id;
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
		$url = sprintf('https://%s/%s/', $this->cdn_host, $this->file_id);
		
		$operations = array();
		
		foreach ($this->operations as $i => $operation_item) {
			$part = array();
			foreach (array_keys($operation_item) as $operation_type) {
				$operation_params = $operation_item[$operation_type];
				$part[] = $operation_type;	
				switch ($operation_type) {
					case 'crop':
						$part = $this->__addPartSize($part, $operation_params);
						$part = $this->__addPartCenter($part, $operation_params);
						$part = $this->__addPartFillColor($part, $operation_params);
						break;
					case 'resize':
						$part = $this->__addPartSize($part, $operation_params);
						break;
					case 'scale_crop':
						$part = $this->__addPartSize($part, $operation_params);
						$part = $this->__addPartCenter($part, $operation_params);
						break;
					case 'effect':
						$part = $this->__addPartEffect($part, $operation_params);
						break;
				}
				$part_str = join('/', $part);
				$operations[] = $part_str;				
			}
		}
		
		if (count($operations)) {
			$operations_part = join('/-/', $operations);
			return $url.'-/'.$operations_part.'/';
		} else {
			return $url;
		}
	}	
	
	/**
	 * Get object with cropped parameters.
	 * 
	 * @param integer $width Crop width
	 * @param integer $height Crop height
	 * @param boolean $center Center crop? true or false (default false).
	 * @param string $fill_color Fill color. If nothig is provided just use false (default false).
	 * @return File
	 */
	public function crop($width, $height, $center = false, $fill_color = false)
	{
		$result = clone $this;
		$result->operations[]['crop'] = array(
				'width' => $width,
				'height' => $height,
				'center' => $center,
				'fill_color' => $fill_color,
		);
		return $result;
	}
	
	/**
	 * Get object with resized parameters.
	 * Provide width or height or both.
	 * If not width or height are provided exceptions will be thrown!
	 * 
	 * @param integer $width Resized image width. Provide false if you resize proportionally.
	 * @param integer $height Resized image height. Provide false if you resize proportionally.
	 * @throws \Exception
	 * @return File
	 **/
	public function resize($width = false, $height = false)
	{
		$result = clone $this;
		if (!$width && !$height) {
			throw \Exception('Please, provide at least width or height for resize');
		}
		$result->operations[]['resize'] = array(
				'width' => $width,
				'height' => $height,
		);
		return $result;		
	}
	
	/**
	 * Get object with cropped parameters.
	 *
	 * @param integer $width Crop width
	 * @param integer $height Crop height
	 * @param boolean $center Center crop? true or false (default false).
	 * @return File
	 */
	public function scaleCrop($width, $height, $center = false)
	{
		$result = clone $this;
		$result->operations[]['scale_crop'] = array(
				'width' => $width,
				'height' => $height,
				'center' => $center,
		);
		return $result;
	}	
	
	/**
	 * Apply flip effect
	 * 
	 * @return File
	 **/
	public function applyFlip()
	{
		$result = clone $this;
		$result->operations[]['effect'] = 'flip';
		return $result;
	}

	/**
	 * Apply grayscale effect
	 *
	 * @return File
	 **/
	public function applyGrayscale()
	{
		$result = clone $this;
		$result->operations[]['effect'] = 'grayscale';
		return $result;
	}

	/**
	 * Apply invert effect
	 *
	 * @return File
	 **/
	public function applyInvert()
	{
		$result = clone $this;
		$result->operations[]['effect'] = 'invert';
		return $result;
	}	
	
	/**
	 * Apply mirror effect
	 *
	 * @return File
	 **/
	public function applyMirror()
	{
		$result = clone $this;
		$result->operations[]['effect'] = 'mirror';
		return $result;
	}	
	
	/**
	 * Adds part with size for operations
	 * 
	 * @param array $part
	 * @param array $params
	 * @return array
	 **/
	private function __addPartSize($part, $params)
	{
		$part[] = sprintf('%sx%s', $params['width'], $params['height']);
		return $part;
	}
	
	/**
	 * Adds part with center for operations
	 *
	 * @param array $part
	 * @param array $params
	 * @return array
	 **/	
	private function __addPartCenter($part, $params)
	{
		if ($params['center'] !== false) {
			$part[] = 'center';
		}		
		return $part;
	}
	
	/**
	 * Adds part with fill color for operations
	 *
	 * @param array $part
	 * @param array $params
	 * @return array
	 **/	
	private function __addPartFillColor($part, $params)
	{
		if ($params['fill_color'] !== false) {
			$part[] = $params['fill_color'];
		}		
		return $part;
	}	
	
	/**
	 * Adds part with effect for operations
	 *
	 * @param array $part
	 * @param string $effect
	 * @return array
	 **/	
	private function __addPartEffect($part, $effect)
	{
		$part[] = $effect;
		return $part;
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