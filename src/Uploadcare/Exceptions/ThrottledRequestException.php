<?php
namespace Uploadcare\Exceptions;

class ThrottledRequestException extends \RuntimeException
{
  const DEFAULT_TIMEOUT = 15;

  protected $responseHeaders = null;

  public function setResponseHeaders(array $response_headers)
  {
    $this->responseHeaders = array_change_key_case($response_headers, CASE_LOWER);
  }

  /**
   * @return int
   */
  public function getTimeout()
  {
    if ($this->isWaitHeaderSet()) {
      return (int) $this->responseHeaders['x-throttle-wait-seconds'];
    }

    return self::DEFAULT_TIMEOUT;
  }

  private function isWaitHeaderSet()
  {
    return $this->responseHeaders && isset($this->responseHeaders['x-throttle-wait-seconds']);
  }
}
