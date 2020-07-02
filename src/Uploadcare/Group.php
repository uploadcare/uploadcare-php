<?php

namespace Uploadcare;

class Group
{
    /**
     * @var string
     */
    private $re_uuid = '!/?(?P<uuid>[a-z0-9]{8}-(?:[a-z0-9]{4}-){3}[a-z0-9]{12}~(?P<files_qty>\d+))!';

    /**
     * Uploadcare group id
     *
     * @var string
     */
    private $uuid = null;

    /**
     * Total files in group
     *
     * @var int
     */
    private $files_qty = null;

    /**
     * Uploadcare class instance.
     *
     * @var Api
    */
    private $api = null;

    /**
     * Cached data
     *
     * @var array
    */
    private $cached_data = null;

    /**
     * Constructs an object with specified ID
     *
     * @param string $uuid_or_url Uploadcare group UUID or CDN URL
     * @param Api $api Uploadcare class instance
     * @throws \Exception
     */
    public function __construct($uuid_or_url, Api $api)
    {
        $matches = array();
        if (!preg_match($this->re_uuid, $uuid_or_url, $matches)) {
            throw new \Exception('UUID not found');
        }

        $this->uuid = $matches['uuid'];
        $this->files_qty = (int)$matches['files_qty'];
        $this->api = $api;
    }

    /**
     * Update info.
     *
     * @throws \Exception
     */
    public function updateInfo()
    {
        $this->cached_data = (array)$this->api->preparedRequest('group', 'GET', array('uuid' => $this->getUuid()));
    }

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
     * Return group UUID for this file
     *
     * @deprecated 1.0.6 Use getUuid instead.
     * @return string
     * @deprecated
     */
    public function getGroupId()
    {
        Helper::deprecate('1.0.6', '3.0.0', 'Use getUuid() instead');
        return $this->getUuid();
    }

    /**
     * Return files_qty
     *
     * @return int
     */
    public function getFilesQty()
    {
        return $this->files_qty;
    }

    /**
     * Try to store group.
     *
     * @throws \Exception
     * @return object|null
     */
    public function store()
    {
        return $this->api->preparedRequest('group_storage', 'POST', array('uuid' => $this->getUuid()));
    }

    /**
     * Get cdn_url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->data['cdn_url'];
    }

    /**
     * Get all Files
     *
     * @throws \Exception
     * @return array
     */
    public function getFiles()
    {
        $result = array();
        foreach ($this->data['files'] as $file) {
            if ($file) {
                $result[] = new File($file->uuid, $this->api, $file);
            }
        }
        return $result;
    }
}
