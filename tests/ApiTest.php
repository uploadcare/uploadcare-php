<?php
error_reporting(E_ALL);
require_once __DIR__.'/config.php';
require_once __DIR__.'/../vendor/autoload.php';

use Uploadcare\Api;
use Uploadcare\File;


class ApiTest extends PHPUnit_Framework_TestCase
{
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
   * This is just some simple test to check that classes are right.
   */
  public function testChildObjectsValid()
  {
    $this->assertTrue(get_class($this->api->widget) == 'Uploadcare\Widget');
    $this->assertTrue(get_class($this->api->uploader) == 'Uploadcare\Uploader');
  }

  /**
   * Is public key valid?
   */
  public function testPublicKeyValid()
  {
    $this->assertTrue($this->api->getPublicKey() == 'demopublickey', 'This is true');
  }

  /**
   * Test that getFilesList method returns array
   * and each item of array is an object of Uploadcare\File class
   */
  public function testFileList()
  {
    $files = $this->api->getFileList();
    $this->assertTrue(is_array($files));
    $this->assertEquals(20, count($files));

    $files = $this->api->getFileList(1, 2);
    $this->assertTrue(is_array($files));
    $this->assertEquals(2, count($files));

    foreach ($files as $file) {
      $this->assertTrue(get_class($file) == 'Uploadcare\File');
    }
  }

  /**
   * Test getFilePaginationInfo method
   */
  public function testFileListPaginationInfo()
  {
    $result = $this->api->getFilePaginationInfo();
    $this->assertEquals(20, $result['per_page']);

    $result = $this->api->getFilePaginationInfo(2, 1);
    $this->assertEquals(1, $result['per_page']);
    $this->assertEquals(2, $result['page']);
  }

  /**
   * Test requests for exceptions to "raw" url
   */
  public function testRequestsRaw()
  {
    // this are request to https://api.uploadcare.com/ url.
    // no exceptions should be thrown
    try {
      $result = $this->api->request('GET', '/');
      $this->api->request('HEAD', '/');
      $this->api->request('OPTIONS', '/');
    } catch (Exception $e) {
      $this->fail('An unexpected exception thrown: ' . $e->getMessage());
    }

    // let's check we have a "resources"
    $this->assertTrue(is_array($result->resources));

    // this are requests to https://api.uploadcare.com/ url.
    // But this requests are now allowed but this url and we must have an exception
    try {
      $this->api->request('POST', '/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }

    try {
      $this->api->request('PUT', '/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }

    try {
      $this->api->request('DELETE', '/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }
  }

  /**
   * Test requests to "project" url
   */
  public function testRequestsProject()
  {
    // this are request to https://api.uploadcare.com/project/ url.
    // no exceptions should be thrown
    try {
      $result = $this->api->request('GET', '/project/');
      $this->api->request('HEAD', '/project/');
      $this->api->request('OPTIONS', '/project/');
    } catch (Exception $e) {
      $this->fail('An unexpected exception thrown');
    }

    // echo $result;
    // we have some data, let's check it
    $this->assertEquals($result->name, 'demo');
    $this->assertEquals($result->pub_key, 'demopublickey');

    // this are requests to https://api.uploadcare.com/project/ url.
    // But this requests are now allowed but this url and we must have an exception
    try {
      $this->api->request('POST', '/project/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }

    try {
      $this->api->request('PUT', '/project/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }

    try {
      $this->api->request('DELETE', '/project/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }
  }

  /**
   * Test request to "files"
   */
  public function testRequestsFiles()
  {
    // this are request to https://api.uploadcare.com/files/ url.
    // no exceptions should be thrown
    try {
      $result = $this->api->request('GET', '/files/');
      // $this->api->request('HEAD', '/files/');
      $this->api->request('OPTIONS', '/files/');
    } catch (Exception $e) {
      $this->fail('An unexpected exception thrown: ' . $e->getMessage());
    }

    // let's check we have an array of raw file data
    $this->assertTrue(is_array($result->results));
    $this->assertGreaterThan(0, count($result->results));
    $file_raw = (array)$result->results[0];
    $this->assertArrayHasKey('size', $file_raw);
    $this->assertArrayHasKey('datetime_uploaded', $file_raw);
    $this->assertArrayHasKey('is_image', $file_raw);
    $this->assertArrayHasKey('uuid', $file_raw);
    $this->assertArrayHasKey('original_filename', $file_raw);
    $this->assertArrayHasKey('mime_type', $file_raw);

    // this are requests to https://api.uploadcare.com/files/ url.
    // But this requests are now allowed but this url and we must have an exception
    try {
      $this->api->request('POST', '/files/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }

    try {
      $this->api->request('PUT', '/files/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }

    try {
      $this->api->request('DELETE', '/files/');
      $this->fail('We must get an exception but everything worked fine!');
    } catch (Exception $e) {
    }
  }

  /**
   * Test setting File with raw data
   */
  public function testFileFromJSON()
  {
    $result = $this->api->request('GET', '/files/');
    $file_raw = (array)$result->results[0];

    $file = new File($file_raw['uuid'], $this->api);
    $this->assertEquals($file_raw['uuid'], $file->data['uuid']);

    $file = new File($file_raw['uuid'], $this->api, $file_raw);
    $this->assertEquals($file_raw['uuid'], $file->data['uuid']);
  }

  /**
   * Let's check the file operations and check for correct urls
   */
  public function testFile()
  {
    $file = $this->api->getFile('3c99da1d-ef05-4d79-81d8-d4f208d98beb');

    $this->assertEquals(get_class($file), 'Uploadcare\File');

    $this->assertEquals($file->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/');
    $this->assertEquals($file->resize(400, 400)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/400x400/');
    $this->assertEquals($file->resize(400, false)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/400x/');
    $this->assertEquals($file->resize(false, 400)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/x400/');

    $this->assertEquals($file->crop(400, 400)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/crop/400x400/');
    $this->assertEquals($file->crop(400, 400, true)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/crop/400x400/center/');
    $this->assertEquals($file->crop(400, 400, true, 'ff0000')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/crop/400x400/center/ff0000/');
    $this->assertEquals($file->crop(400, 400, false, 'ff0000')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/crop/400x400/ff0000/');

    $this->assertEquals($file->scaleCrop(400, 400)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/scale_crop/400x400/');
    $this->assertEquals($file->scaleCrop(400, 400, true)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/scale_crop/400x400/center/');

    $this->assertEquals($file->effect('flip')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/effect/flip/');
    $this->assertEquals($file->effect('grayscale')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/effect/grayscale/');
    $this->assertEquals($file->effect('invert')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/effect/invert/');
    $this->assertEquals($file->effect('mirror')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/effect/mirror/');

    $this->assertEquals($file->effect('flip')->effect('mirror')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/effect/flip/-/effect/mirror/');
    $this->assertEquals($file->effect('mirror')->effect('flip')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/effect/mirror/-/effect/flip/');

    $this->assertEquals($file->resize(400, 400)->scaleCrop(200, 200, true)->effect('mirror')->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/400x400/-/scale_crop/200x200/center/-/effect/mirror/');

    $this->assertEquals($file->preview(400, 400)->getUrl(), 'http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/preview/400x400/');
  }

  /**
   * Let's check that user can set custom CDN host
   */
  public function testCustomCDNHost()
  {
    $this->api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY, null, 'example.com');
    $file = $this->api->getFile('3c99da1d-ef05-4d79-81d8-d4f208d98beb');

    $this->assertEquals(get_class($file), 'Uploadcare\File');

    $this->assertEquals($file->getUrl(), 'http://example.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/');
    $this->assertEquals($file->resize(400, 400)->getUrl(), 'http://example.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/400x400/');
    $this->assertEquals($file->resize(400, false)->getUrl(), 'http://example.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/400x/');
    $this->assertEquals($file->resize(false, 400)->getUrl(), 'http://example.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/resize/x400/');
    
    // Check user can use full-featured CDN host
    $api->setCdnHost('https://user:pass@example.com:4433/path/');
    $this->assertEquals($file->getUrl(), 'https://user:pass@example.com:4433/path/3c99da1d-ef05-4d79-81d8-d4f208d98beb/');
  }

  /**
   * Test upload from URL
   */
  public function testUploadFromURL()
  {
    try {
      $file = $this->api->uploader->fromUrl('http://www.baysflowers.co.nz/Images/tangerine-delight.jpg');
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to upload from url: '.$e->getMessage());
    }
    $this->assertEquals(get_class($file), 'Uploadcare\File');
    try {
      $file->store();
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to store uploaded file from url: '.$e->getMessage());
    }

    // test file delete
    try {
      $this->assertNull($file->data['datetime_removed']);
      $file->delete();
      $file->updateInfo();
      $this->assertNotNull($file->data['datetime_removed']);

    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to delete file: '.$e->getMessage());
    }

  }

  /**
   * Test uploading from path
   */
  public function testUploadFromPath()
  {
    try {
      $file = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to upload from path: '.$e->getMessage());
    }

    try {
      $file->store();
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to store uploaded file from path: '.$e->getMessage());
    }
  }

  public function testUploadFromResource()
  {
    try {
      $fp = fopen(dirname(__FILE__).'/test.jpg', 'r');
      $file = $this->api->uploader->fromResource($fp);
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to upload from resource: '.$e->getMessage());
    }
    try {
      $file->store();
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to store uploaded file from resource: '.$e->getMessage());
    }
  }

  public function testUploadFromString()
  {
    try {
      $content = "This is some text I want to upload";
      $file = $this->api->uploader->fromContent($content, 'text/plain');
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to upload from contents: '.$e->getMessage());
    }
    try {
      $file->store();
    } catch (Exception $e) {
      $this->fail('We get an unexpected exception trying to store uploaded file from contents: '.$e->getMessage());
    }

    usleep(500);
    $text = file_get_contents($file->getUrl());
    $this->assertEquals($text, "This is some text I want to upload");
  }

  public function testFileConstructor()
  {
    $f = $this->api->getFile('3c99da1d-ef05-4d79-81d8-d4f208d98beb');
    $this->assertEquals('3c99da1d-ef05-4d79-81d8-d4f208d98beb', $f->getUuid());

    $f = $this->api->getFile('http://www.ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/preview/100x100/-/effect/grayscale/bill.jpg');
    $this->assertEquals('3c99da1d-ef05-4d79-81d8-d4f208d98beb', $f->getUuid());
    $this->assertEquals('preview/100x100/-/effect/grayscale/', $f->default_effects);
    $this->assertEquals('bill.jpg', $f->filename);
  }

  public function testGroupConstructor()
  {
    $g = $this->api->getGroup('cd334b26-c641-4393-bcce-b5041546430d~11');
    $this->assertEquals('cd334b26-c641-4393-bcce-b5041546430d~11', $g->getUuid());
    $this->assertEquals(11, $g->getFilesQty());

    $g = $this->api->getGroup('http://www.ucarecdn.com/cd334b26-c641-4393-bcce-b5041546430d~11/');
    $this->assertEquals('cd334b26-c641-4393-bcce-b5041546430d~11', $g->getUuid());
    $this->assertEquals(11, $g->getFilesQty());
  }

  public function testGroupApi()
  {
    $f1 = $this->api->uploader->fromContent('1', 'text/plain');
    $f2 = $this->api->uploader->fromContent('2', 'text/plain');

    $g = $this->api->uploader->createGroup(array($f1, $f2));

    $uuid = $g->getUuid();
    $this->assertEquals(2, $g->getFilesQty());
    $this->assertNull($g->data['datetime_stored']);

    $g->store();
    $g->updateInfo();

    $this->assertNotNull($g->data['datetime_stored']);

    $g->getFiles();
  }
}
