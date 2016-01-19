<?php
error_reporting(E_ALL);
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../../vendor/autoload.php';

use Uploadcare\Exceptions\ThrottledRequestException;

class ThrottledRequestExceptionTest extends \PHPUnit_Framework_TestCase
{
  /** @var ThrottledRequestException */
  private $throttled_request_exception;

  protected function setUp()
  {
    $this->throttled_request_exception = new ThrottledRequestException();
  }

  public function testTimeOutIsRetrievedFromHeaderIfPresent()
  {
    $wait = 10;

    $this->throttled_request_exception->setResponseHeaders(array('X-Throttle-Wait-Seconds' => $wait));

    $this->assertEquals($wait, $this->throttled_request_exception->getTimeout());
  }

  /**
   * @param array $headers
   * @dataProvider headersProvider
   */
  public function testDefaultTimeoutIsRetrievedIfNoHeaderIsSet($headers)
  {
    if (is_array($headers)) {
      $this->throttled_request_exception->setResponseHeaders($headers);
    }

    $this->assertEquals(ThrottledRequestException::DEFAULT_TIMEOUT, $this->throttled_request_exception->getTimeout());
  }

  public function headersProvider()
  {
    return array(
      'no headers set' => array(null),
      'wait header is not set' => array(array('allow' => 'GET, HEAD, OPTIONS'))
    );
  }
}