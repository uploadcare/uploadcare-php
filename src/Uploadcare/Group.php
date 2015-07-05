<?php
namespace Uploadcare;

class Group
{
  /**
   * @var string
   */
  private $re_uuid = '!/?(?P<uuid>[a-z0-9]{8}-(?:[a-z0-9]{4}-){3}[a-z0-9]{12}~(?P<files_qty>\d+))!';

  /**
   * Uploadcare cdn host
   *
   * @var string
   */
  private $cdn_host = 'www.ucarecdn.com';

  /**
   * Uploadcare group id
   *
   * @var string
   */
  private $group_id = null;

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
   * @param string $uuid_or_url Uploadcare group_id or CDN URL
   * @param Api $api Uploadcare class instance
   * @throws \Exception
   */
  public function __construct($uuid_or_url, Api $api)
  {
    $matches = array();
    if (!preg_match($this->re_uuid, $uuid_or_url, $matches)) {
      throw new \Exception('UUID not found');
    }

    $this->group_id = $matches['uuid'];
    $this->files_qty = (int)$matches['files_qty'];
    $this->api = $api;
  }

  public function __get($name)
  {
    if ($name == 'data') {
      if (!$this->cached_data) {
        $this->cached_data = (array)$this->api->__preparedRequest('group', 'GET', array('uuid' => $this->group_id));
      }
      return $this->cached_data;
    }
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getUrl();
  }

  /**
   * Return group_id for this file
   *
   * @return string
   */
  public function getGroupId()
  {
    return $this->group_id;
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
   * @return array
   */
  public function store()
  {
    return $this->api->__preparedRequest('group_storage', 'POST', array('uuid' => $this->group_id));
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
   * @return array
   **/
  public function getFiles()
  {
    $result = array();
    foreach ($this->data['files'] as $file) {
      if ($file) {
        $result[] = new File($file->uuid, $this->api);
      }
    }
    return $result;
  }
}
