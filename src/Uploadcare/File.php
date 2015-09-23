<?php
namespace Uploadcare;


class File
{
  private $re_uuid_with_effects = '!/?(?P<uuid>[a-z0-9]{8}-(?:[a-z0-9]{4}-){3}[a-z0-9]{12})(?:/(?:-/(?P<effects>(?:[^/]+/)+)))?(?<filename>[^/]*)!';

  /**
   * Uploadcare file id
   *
   * @var string
   */
  private $uuid = null;

  /**
   * Operations and params for operations.
   *
   * @var array
   */
  private $operations = array();

  /**
   * Uploadcare class instance.
   *
   * @var Api
   */
  private $api = null;

  /**
   * Operations list
   */
  private $operation_list = array('crop',
                                  'resize',
                                  'scale_crop',
                                  'effect',
                                  'preview');

  /**
   * Cached data
   *
   * @var array
   */
  private $cached_data = null;

  /**
   * Constructs an object for CDN file with specified ID
   *
   * @param string $uuid_or_url Uploadcare file UUID or CDN URL
   * @param Api $api Uploadcare class instance
   * @param boolean|array|object $data prepopulate this->cached_data
   * @throws \Exception
   */
  public function __construct($uuid_or_url, Api $api, $data = false)
  {
    $matches = array();
    if(!preg_match($this->re_uuid_with_effects, $uuid_or_url, $matches)) {
      throw new \Exception('UUID not found');
    }

    $this->uuid = $matches['uuid'];
    $this->default_effects = $matches['effects'];
    $this->filename = $matches['filename'];
    $this->api = $api;
    if ($data) {
      $this->cached_data = (array)$data;
    }
  }

  /**
   * @param $name
   * @return array|null
   */
  public function __get($name)
  {
    if ($name == 'data') {
      if (!$this->cached_data) {
        $this->updateInfo();
      }
      return $this->cached_data;
    }
    return null;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getUrl();
  }

  /**
   * Get UUID
   *
   * @returns string
   */
  public function getUuid()
  {
    return $this->uuid;
  }

  /**
   * Get UUID
   *
   * @deprecated
   * @return string
   */
  public function getFileId()
  {
    return $this->getUuid();
  }

  /**
   * Update File info
   *
   * @return array
   */
  public function updateInfo()
  {
    $this->cached_data = (array)$this->api->__preparedRequest('file', 'GET', array('uuid' => $this->uuid));
    return $this->cached_data;
  }

  /**
   * Store file.
   *
   * @return array
   */
  public function store()
  {
    return $this->api->__preparedRequest('file_storage', 'POST', array('uuid' => $this->uuid));
  }

  /**
   * Copy the file.
   *
   * @param string $target Name of custom storage.
   * @return File|string
   */
  public function copy($target = null)
  {
    return $this->api->copyFile($this->getUrl(), $target);
  }

  /**
   * Copy the file to custom storage.
   *
   * @param string $target Name of custom storage.
   * @return string
   */
  public function copyTo($target)
  {
    return (string)$this->copy($target);
  }

  /**
   * Delete file
   *
   * @return array
   */
  public function delete()
  {
    return $this->api->__preparedRequest('file_storage', 'DELETE', array('uuid' => $this->uuid));
  }

  /**
   * Get url of original image
   *
   * @param string $postfix
   * @return string
   */
  public function getUrl($postfix = null)
  {
    $url = sprintf('%s/%s/', $this->api->getCdnUri(), $this->uuid);
    if($this->default_effects) {
      $url = sprintf('%s-/%s', $url, $this->default_effects);
    }
    if($this->filename && $postfix === null) {
      $postfix = $this->filename;
    }

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

          case 'preview':
            $part = $this->__addPartSize($part, $operation_params);
            break;

          case 'custom':
            $part = array($operation_params);
            break;
        }
        $part_str = join('/', $part);
        $operations[] = $part_str;
      }
    }

    if (count($operations)) {
      $operations_part = join('/-/', $operations);
      return $url.'-/'.$operations_part.'/'.$postfix;
    } else {
      return $url.$postfix;
    }
  }

  /**
   * Get image tag
   *
   * @param string $postfix File path postfix
   * @param array $attribs additional attributes
   * @return string
   */
  public function getImgTag($postfix = null, $attribs = array())
  {
    $to_compile = array();
    foreach ($attribs as $key => $value) {
      $to_compile[] = sprintf('%s="%s"', $key, $value);
    }
    return sprintf('<img src="%s" %s />', $this->getUrl(), join(' ', $to_compile));
  }

  /**
   * Get object with cropped parameters.
   *
   * @param int $width Crop width
   * @param int $height Crop height
   * @param boolean $center Center crop? true or false (default false).
   * @param string|boolean $fill_color Fill color. If nothing is provided just use false (default false).
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
   * @param int|boolean $width Resized image width. Provide false if you resize proportionally.
   * @param int|boolean $height Resized image height. Provide false if you resize proportionally.
   * @throws \Exception
   * @return File
   */
  public function resize($width = false, $height = false)
  {
    $result = clone $this;
    if (!$width && !$height) {
      throw new \Exception('Please, provide at least width or height for resize');
    }
    $result->operations[]['resize'] = array(
        'width' => $width,
        'height' => $height,
    );
    return $result;
  }

  /**
   * Get object with preview parameters.
   * Provide both width and height.
   * If no width and height are provided exceptions will be thrown!
   *
   * @param int $width Preview image width.
   * @param int $height Preview image height.
   * @throws \Exception
   * @return File
   */
  public function preview($width, $height)
  {
    $result = clone $this;
    if (!$width || !$height) {
      throw new \Exception('Please, provide both width and height for preview');
    }
    $result->operations[]['preview'] = array(
        'width' => $width,
        'height' => $height,
    );
    return $result;
  }

  /**
   * Get object with cropped parameters.
   *
   * @param int $width Crop width
   * @param int $height Crop height
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
   * Apply effect
   *
   * @param string $effect Effect name
   * @return File
   */
  public function effect($effect)
  {
    $result = clone $this;
    $result->operations[]['effect'] = $effect;
    return $result;
  }

  /**
   * Add any custom operation.
   *
   * @param string $operation
   * @return File
   */
  public function op($operation)
  {
    $result = clone $this;
    $result->operations[]['custom'] = $operation;
    return $result;
  }
  
  /**
   * Adds part with size for operations
   *
   * @param array $part
   * @param array $params
   * @return array
   */
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
   */
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
   */
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
   */
  private function __addPartEffect($part, $effect)
  {
    $part[] = $effect;
    return $part;
  }
}
