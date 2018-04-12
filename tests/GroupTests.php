<?php
error_reporting(E_ALL);
require_once __DIR__.'/config.php';
require_once __DIR__.'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Uploadcare\Api;
use Uploadcare\Exceptions\ThrottledRequestException;
use Uploadcare\File;

class GroupTest extends TestCase
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
        $this->apiMock = $this->getMockBuilder('\Uploadcare\Api')
            ->disableOriginalConstructor()
            ->setMethods(array('request'))
            ->getMock();
    }

    /**
     * Tear down
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Test that testFileGroupList method returns array
     * and each item of array is an object of Uploadcare\Group class
     */
    public function testFileGroupList()
    {
        $groups = $this->api->getGroupList(array(
            'limit' => 20,
        ));
        $this->assertFalse(is_array($groups));
        $this->assertTrue(is_object($groups));
        $this->assertTrue($groups instanceof \Iterator);
        $this->assertTrue($groups instanceof Uploadcare\GroupIterator);

        $groups = $this->api->getGroupList(array(
            'limit' => 2,
        ));
        $this->assertFalse(is_array($groups));
        $this->assertTrue(is_object($groups));
        $this->assertTrue($groups instanceof \Iterator);
        $this->assertTrue($groups instanceof Uploadcare\GroupIterator);

        foreach ($groups as $g) {
            $this->assertTrue(get_class($g) == 'Uploadcare\Group');
        }
    }

    /**
     * Test Group with usage of ?? for its data
     */
    public function testGroupDataWithNullableChecker()
    {
        $fakeUuid = '8b1362ed-b477-4a15-819a-2c6bb497d8bb~3';
        $defaultValue = ['files' => 'files not found'];

        $file = new \Uploadcare\Group($fakeUuid, $this->api);
        $data = $file->data ?? $defaultValue;
        $this->assertEquals($data, $defaultValue);
    }
}
