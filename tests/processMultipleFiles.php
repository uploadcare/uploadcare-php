<?php
error_reporting(E_ALL);
require_once __DIR__.'/config.php';
require_once __DIR__.'/../vendor/autoload.php';

use Uploadcare\Api;
use Uploadcare\File;
use Uploadcare\Exceptions\ThrottledRequestException;
use PHPUnit\Framework\TestCase;


class ProcessMultipleFilesTest extends TestCase
{
  /** @var Uploadcare\Api */
  private $api;
  /** @var \Uploadcare\Api | \PHPUnit_Framework_MockObject_MockObject */
  private $apiMock;

  /**
   * Setup test
   * @return void
   */
  public function setUp() {
    $this->api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
  }

  /**
   * Tear down
   * @return void
   */
  public function tearDown() {
  }

  
  /**
   * Test that testFileGroupList method returns array
   * and each item of array is an object of Uploadcare\Group class
   */
  public function testMultipleStore()
  {
    $filesArray = array();
    try {
      $f1 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
      $filesArray [] = $f1->getUuid();
      $f2 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
      $filesArray [] = $f2->getUuid();
      $fRes = $this->api->storeMultipleFiles($filesArray);
      $this->assertTrue(is_array($fRes));
      $this->assertTrue(is_array($fRes['files']));
      foreach ($fRes['files'] as $f) {
        $this->assertTrue(get_class($f) == 'Uploadcare\File');
      }
      $this->assertEquals(2, count($fRes['files']));
      $f1->delete();
      $f2->delete();
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to store multiple files: '.$e->getMessage());
    }
  }

  public function testMultipleDelete()
  {
    $filesArray = array();
    try {
      $f1 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
      $filesArray[] = $f1->getUuid();
      $f2 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
      $filesArray[] = $f2->getUuid();
      $fRes = $this->api->deleteMultipleFiles($filesArray);
      $this->assertTrue(is_array($fRes));
      $this->assertTrue(is_array($fRes['files']));
      foreach ($fRes['files'] as $f) {
        $this->assertTrue(get_class($f) == 'Uploadcare\File');
      }
      $this->assertEquals(2, count($fRes['files']));
      
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to delete multiple files: '.$e->getMessage());
    }
  }
}
