<?php
namespace Uploadcare;

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