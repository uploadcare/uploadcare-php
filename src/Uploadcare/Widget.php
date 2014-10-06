<?php
namespace Uploadcare;

class Widget
{
  /**
   * Api instance
   *
   * @var Api
   */
  private $api = null;

  /**
   * Uploadcare widget version
   * @var string
   */
  private $version = '1.4.2';

  /**
   * Constructor
   *
   * @param Api $api
   */
  public function __construct(Api $api)
  {
    $this->api = $api;
  }

  /**
   * Returns <script> sections to include Uploadcare widget
   *
   * @param string $version Uploadcare version
   * @return string
   */
  public function getScriptTag($version = null, $async = false, $locale = null)
  {
    $async_attr = $async ? 'async="true"' : '';
    $locale = !isset($locale) ? $locale = 'en';
    $result = <<<EOT
<script>UPLOADCARE_PUBLIC_KEY = "{$this->api->getPublicKey()}"; UPLOADCARE_LOCALE = "{$locale}"</script>
<script {$async_attr} src="{$this->getScriptSrc($version)}" charset="UTF-8"></script>
EOT;
    return $result;
  }

  /**
   * Return url for javascript widget.
   * If no version is provided method will use default(current) version
   *
   * @param string $version Version of Uploadcare.com widget
   * @return string
   */
  public function getScriptSrc($version = null)
  {
    if (!$version) {
      $version = $this->version;
    }
    return sprintf('https://ucarecdn.com/widget/%s/uploadcare/uploadcare-%s.min.js', $version, $version);
  }

  /**
   * Gets input tag to use with widget
   *
   * @param string $name Input name
   * @param array $attribs Custom attributes to include
   * @return string
   */
  public function getInputTag($name, $attribs = array())
  {
    $to_compile = array();
    foreach ($attribs as $key => $value) {
      $to_compile[] = sprintf('%s="%s"', $key, $value);
    }
    return sprintf('<input type="hidden" role="uploadcare-uploader" name="%s" data-upload-url-base="" %s />', $name, join(' ', $to_compile));
  }
}
