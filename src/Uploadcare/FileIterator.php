<?php
namespace Uploadcare;


class FileIterator implements \Iterator,\Countable,\ArrayAccess
{
  /**
   * Current iterator position
   *
   * @var int
   */
  protected $position = 0;

  /**
   * Array of cached file elements
   *
   * @var array
   */
  protected $container = array();

  /**
   * Total count of files
   *
   * @var int
   */
  protected $count = null;

  /**
   * Desired count of elements
   *
   * @var int
   */
  protected $limit = null;

  /**
   * Limit for requests
   *
   * @var int
   */
  protected $requestLimit = null;

  /**
   * Options for requests
   *
   * @var array
   */
  protected $options = array();

  /**
   * Indicates if all files were loaded from server
   *
   * @var bool
   */
  protected $fullyLoaded = false;

  /**
   * Determines direction of file browsing and which URL param is used: 'from' or 'to'
   *
   * @var bool
   */
  protected $reverse = false;

  /**
   * Api object
   *
   * @var Api
   */
  protected $api;

  /**
   * Next page params array
   *
   * @var array
   */

  protected $nextPageParams = array();

   /**
   * Preview page params array
   *
   * @var array
   */

  protected $prevPageParams = array();


  /**
   * Constructor
   *
   * @param Api $api Uploadcare class instance
   * @param array $options Request parameters and filters
   */
  public function __construct(Api $api, $options = array())
  {
    $this->api = $api;

    $this->limit = $options['limit'];
    $this->requestLimit = $options['request_limit'] ?: $options['limit'];
    unset($options['request_limit']);

//    $this->reverse = $options['to'] && !$options['from'];

    $options['limit'] = $this->requestLimit;

    $this->options = $options;
  }

  public function rewind()
  {
    $this->position = 0;
  }

  public function current()
  {
    return $this->container[$this->position];
  }

  public function key()
  {
    return $this->position;
  }

  public function next()
  {
    ++$this->position;
  }

  public function valid()
  {
    if (!$this->exists() && !$this->isFullyLoaded()) {
      $this->loadChunk();
    }

    return $this->exists();
  }

  /**
   * Check if element exists. Uses current position if no offset provided
   *
   * @param int $offset
   * @return bool
   */
  private function exists($offset = null)
  {
    return isset($this->container[$offset !== null ? $offset : $this->position]);
  }

  /**
   * Check if all elements are loaded into iterator
   *
   * @return bool
   */
  private function isFullyLoaded()
  {
    return $this->fullyLoaded || ($this->limit && count($this->container) >= $this->limit);
  }

  /**
   * Load elements chunk from server
   */
  private function loadChunk()
  {
    $portion = $this->api->getFilesChunk($this->options, $this->reverse);

    $this->options = $portion['params'];
    $this->nextPageParams = $portion['params'];
    $this->prevPageParams = $portion['prevParams'];

    if ($portion['files']) {
      $this->container = array_merge($this->container, $portion['files']);
    }

    if (!count($portion['params'])) {
      $this->fullyLoaded = true;
    }
  }

  public function count()
  {
    if ($this->count === null) {
      $this->count = $this->api->getFilesCount($this->options);
    }

    if ($this->limit && $this->count > $this->limit) {
      $this->count = $this->limit;
    }

    return $this->count;
  }

  public function offsetExists($offset)
  {
    while (!$this->exists($offset) && !$this->isFullyLoaded()) {
      $this->loadChunk();
    }

    return $this->exists($offset);
  }

  public function offsetGet($offset)
  {
    return $this->offsetExists($offset) ? $this->container[$offset] : null;
  }

  public function offsetSet($offset, $value)
  {
    if (is_null($offset)) {
      $this->container[] = $value;
    } else {
      $this->container[$offset] = $value;
    }
  }

  public function offsetUnset($offset)
  {
    unset($this->container[$offset]);
  }

  public function getNextPageParams()
  {
    return $this->nextPageParams;
  }
  public function getPrevPageParams()
  {
    return $this->prevPageParams;
  }
}
