<?php
error_reporting(E_ALL);
require_once __DIR__.'/config.php';
require_once __DIR__.'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Uploadcare\Api;
use Uploadcare\Exceptions\ThrottledRequestException;
use Uploadcare\File;

class CopyFilesTest extends TestCase
{
    /** @var Uploadcare\Api */
    private $api;
    /** @var \Uploadcare\Api | \PHPUnit_Framework_MockObject_MockObject */
    private $apiMock;

    /**
     * Setup test
     * @return void
     */
    public function setUp()
    {
        $this->api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
    }

    /**
     * Tear down
     * @return void
     */
    public function tearDown()
    {
    }


    /**
     * Test that createLocalCopy method returns instanse of Uploadcare\File
     */
    public function testCreateLocalCopyFromApi()
    {
        try {
            $f1 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
            $fileUuid = $f1->getUuid();
            usleep(3000000); // wait 3 sec to give a time to prepare file and avoid error: "File is not ready yet."
            $fRes = $this->api->createLocalCopy($fileUuid, true);
            $f1->delete();
            $this->assertTrue(get_class($fRes) == 'Uploadcare\File');
        } catch (Exception $e) {
            $this->fail('We got an unexpected exception at `Api::createLocalCopy` trying to create local copy of file: '.$e->getMessage());
        }
    }

    /**
     * Test that createRemoteCopy method returns string
     */
    public function testCreateRemoteCopyFromApi()
    {
        $apiMock = $this->getMockBuilder('\Uploadcare\Api')
            ->disableOriginalConstructor()
            ->setMethods(array('request'))
            ->getMock();
        $mockRes = json_decode('{
      "type": "url",
      "result": "s3://mybucket/03ccf9ab-f266-43fb-973d-a6529c55c2ae/image.resize_20x.png"
    }');
        $apiMock->expects($this->once())->method('request')->willReturn($mockRes);
        try {
            $f1 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
            $fileUuid = $f1->getUuid();
            $fRes = $apiMock->createRemoteCopy($fileUuid, "mybucket", true, '${default}');
            $f1->delete();
            $this->assertTrue($fRes == $mockRes->result);
        } catch (Exception $e) {
            $this->fail('We got an unexpected exception at `Api::createRemoteCopy` trying to create remote copy of file: '.$e->getMessage());
        }
    }
    /**
     * Test that createLocalCopy method returns instanse of Uploadcare\File
     */
    public function testCreateLocalCopyFromFile()
    {
        try {
            $f1 = $this->api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
            usleep(3000000); // wait 3 sec to give a time to prepare file and avoid error: "File is not ready yet."
            $fRes = $f1->createLocalCopy(true);
            $f1->delete();
            $this->assertTrue(get_class($fRes) == 'Uploadcare\File');
            $fRes->delete();
        } catch (Exception $e) {
            $this->fail('We got an unexpected exception at: `File::createLocalCopy` trying to create local copy of file in: '.$e->getMessage());
        }
    }
}
