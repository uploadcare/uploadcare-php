<?php

namespace Uploadcare;
use Uploadcare\Authenticate\AkamaiAuthenticatedUrl;

/**
 * @property array $data File info
 */
class File
{
    /**
     * @var string
     */
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
     * AuthenticatedUrl concrete realization
     *
     * @var Authenticate\AuthenticatedUrlInterface
     */
    private $authenticatedUrl;

    /**
     * Operations list
     */
    private $operation_list = array(
        'crop',
        'resize',
        'scale_crop',
        'effect',
        'preview',
    );

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
        if (!preg_match($this->re_uuid_with_effects, $uuid_or_url, $matches)) {
            throw new \Exception(sprintf('UUID or CDN URL cannot be found in "%s"', $uuid_or_url));
        }

        $this->uuid = $matches['uuid'];
        $this->default_effects = $matches['effects'];
        $this->filename = $matches['filename'];
        $this->api = $api;
        if ($data) {
            $this->cached_data = (array)$data;

            if (array_key_exists('default_effects', $this->cached_data)) {
                $this->default_effects = $this->cached_data['default_effects'];
            }
        }

        if ($api->cdn_secret_token) {
            if(empty($api->cdn_host)) {
                throw new \Exception('CDN Host must not be empty');
            }

            if ($api->cdn_provider === 'akamai') {
                $this->authenticatedUrl = new AkamaiAuthenticatedUrl($this->api->cdn_secret_token, $this->api->lifetime);
            } else {
                throw new \Exception('Not Implemented');
            }
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

    public function __isset($name)
    {
        return $name == 'data';
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
     * @deprecated 1.0.6 Use getUuid() instead.
     * @return string
     */
    public function getFileId()
    {
        Helper::deprecate('1.0.6', '3.0.0', 'Use getUuid() instead');
        return $this->getUuid();
    }

    /**
     * Update File info
     *
     * @return array
     * @throws \Exception
     */
    public function updateInfo()
    {
        $this->cached_data = (array)$this->api->__preparedRequest('file', 'GET', array('uuid' => $this->uuid));
        return $this->cached_data;
    }

    /**
     * Store file.
     *
     * @return object
     * @throws \Exception
     */
    public function store()
    {
        return $this->api->__preparedRequest('file_storage', 'POST', array('uuid' => $this->uuid));
    }

    /**
     * Copy the file.
     *
     * @deprecated 2.0.0 Use createLocalCopy() or createRemoteCopy() instead.
     * @param string $target Name of custom storage.
     * @return File|string
     * @throws \Exception
     */
    public function copy($target = null)
    {
        Helper::deprecate('2.0.0', '3.0.0', 'Use createLocalCopy() or createRemoteCopy() instead');
        return $this->api->copyFile($this->getUrl(), $target);
    }

    /**
     * Copy the file to custom storage.
     *
     * @deprecated 2.0.0 Use createRemoteCopy() instead.
     * @param string $target Name of custom storage.
     * @return string
     * @throws \Exception
     */
    public function copyTo($target)
    {
        Helper::deprecate('2.0.0', '3.0.0', 'Use createRemoteCopy() instead');
        return (string)$this->copy($target);
    }

    /**
     * Copy file to the Uploadcare storage
     *
     * @param boolean $store MUST be either true or false. true to store files while copying. If stored, files won’t be automatically deleted within 24 hours after copying. false * to not store files, default.
     * @return File|string
     * @throws \Exception
     */
    public function createLocalCopy($store = true)
    {
        return $this->api->createLocalCopy($this->getUrl(), $store);
    }

    /**
     * Copy file to the external storage
     *
     * @param string $target Name of custom storage connected to your project.
     * @param boolean $make_public (Optional) MUST be either true or false. true to make copied files available via public links. false to reverse the behavior.
     * @param string $pattern (Optional) The parameter is used to specify file names Uploadcare passes to custom storages. In case parameter is omitted, custom storage pattern is used.
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
    public function createRemoteCopy($target, $make_public = true, $pattern = '${default}')
    {
        return $this->api->createRemoteCopy($this->getUrl(), $target, $make_public, $pattern);
    }

    /**
     * Delete file
     *
     * @return object|null
     * @throws \Exception
     */
    public function delete()
    {
        return $this->api->__preparedRequest('file_storage', 'DELETE', array('uuid' => $this->uuid));
    }

    /**
     * Get URL of original image
     *
     * @param string $postfix
     * @return string
     */
    public function getUrl($postfix = null)
    {
        if (!empty($this->authenticatedUrl)) {
            $path = $this->authenticatedUrl->getAuthenticatedUrl($this->getPath($postfix));
        } else {
            $path = $this->getPath($postfix);
        }
        $url = sprintf('%s%s', $this->api->getCdnUri(), $path);
        return $url;
    }

    /**
     * Get local URL path of original image
     *
     * @param string $postfix
     * @return string
     */
    public function getPath($postfix = null)
    {
        $url = sprintf('/%s/', $this->uuid);
        if ($this->default_effects) {
            $url = sprintf('%s-/%s', $url, $this->default_effects);
        }
        if ($this->filename && $postfix === null) {
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
     * @param array $attributes additional attributes
     * @return string
     */
    public function getImgTag($postfix = null, $attributes = array())
    {
        $to_compile = array();
        foreach ($attributes as $key => $value) {
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
     * @throws \Exception
     */
    public function crop($width, $height, $center = false, $fill_color = false)
    {
        if (!$width || !$height) {
            throw new \Exception('Please provide both $width and $height');
        }
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
        if (!$width && !$height) {
            throw new \Exception('Please, provide at least $width or $height for resize');
        }
        $result = clone $this;
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
        if (!$width || !$height) {
            throw new \Exception('Please, provide both width and height for preview');
        }
        $result = clone $this;
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
     * @throws \Exception
     */
    public function scaleCrop($width, $height, $center = false)
    {
        if (!$width || !$height) {
            throw new \Exception('Please, provide both $width and $height');
        }
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
        if (!$effect) {
            return $this;
        }
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
        if (!$operation) {
            return $this;
        }
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
