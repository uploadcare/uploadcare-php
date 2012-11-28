<?php
namespace Uploadcare;

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
	 * @var Widget
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