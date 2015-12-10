<?php
namespace Uploadcare;

class ThrottledRequestException extends \RuntimeException
{
  const DEFAULT_TIMEOUT = 15;

  protected $responseHeaders = null;

  /**
   * @param string|array $response
   */
  public function setResponseHeaders($response)
  {
    $this->responseHeaders = array_change_key_case(is_array($response) ? $response : \Uploadcare\Helper::parseHttpHeaders($response), CASE_LOWER);
  }

  /**
   * @return null|array
   */
  public function getResponseHeaders()
  {
    return $this->responseHeaders;
  }

  /**
   * @return int|null
   */
  public function getTimeout()
  {
    return $this->responseHeaders && isset($this->responseHeaders['x-throttle-wait-seconds'])
      ? (int)$this->responseHeaders['x-throttle-wait-seconds']
      : self::DEFAULT_TIMEOUT;
  }
}
