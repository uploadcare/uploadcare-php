<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Uploadcare\Api;
use Uploadcare\DataClass\MultipartStartResponse;
use Uploadcare\MultipartUpload;
use Uploadcare\Uploader;

class MultipartUploadTest extends TestCase
{
    /**
     * @var Api
     */
    private $api;

    protected function setUp()
    {
        $this->api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
    }

    public function testKeys()
    {
        $this->assertEquals('demopublickey', $this->api->getPublicKey());
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function getSignedUploadArray()
    {
        $reflection = new ReflectionObject($this->api->uploader);
        $getSignedUploadsData = $reflection->getMethod('getSignedUploadsData');
        $getSignedUploadsData->setAccessible(true);

        return $getSignedUploadsData->invokeArgs($this->api->uploader, array(
            array(Uploader::UPLOADCARE_PUB_KEY_KEY => $this->api->getPublicKey(), Uploader::UPLOADCARE_STORE_KEY => 'auto'),
        ));
    }

    protected function getHost()
    {
        $reflection = new ReflectionObject($this->api->uploader);
        $host = $reflection->getProperty('host');
        $host->setAccessible(true);
        return $host->getValue($this->api->uploader);
    }

    public function testStartRequestDataMethod()
    {
        $requestData = $this->getSignedUploadArray();
        $mu = new MultipartUpload($requestData, $this->getHost(), $this->api->uploader);

        $muReflection = new ReflectionObject($mu);
        $startRequestData = $muReflection->getMethod('startRequestData');
        $startRequestData->setAccessible(true);

        $path = __DIR__ . '/test.jpg';
        $size = \filesize($path);
        $mimeType = 'image/jpeg';

        $result = $startRequestData->invokeArgs($mu, array($size, $mimeType, 'test.jpg'));
        $this->assertArrayHasKey('filename', $result);
        $this->assertEquals('test.jpg', $result['filename']);
        $this->assertArrayHasKey(Uploader::UPLOADCARE_PUB_KEY_KEY, $result);
    }

    /**
     * @todo Refactor this. Works only with valid keys, not with demo, and you should check `startRequestData`
     *       method for valid data and then mock Uploader::runRequest for return tests/data/startResponse.json contents.
     *
     * @throws \ReflectionException
     */
    public function testRealStartRequest()
    {
//        $mu = new MultipartUpload($this->getSignedUploadArray(), $this->getHost(), $this->api->uploader);
//        $muReflection = new ReflectionObject($mu);
//        $startRequestData = $muReflection->getMethod('startRequestData');
//        $startRequestData->setAccessible(true);
//
//        $path = __DIR__ . '/24hr-NewYork-5K.heic';
//        $size = \filesize($path);
//        $mimeType = 'image/heic-sequence';
//        $data = $startRequestData->invokeArgs($mu, array($size, $mimeType, '24hr-NewYork-5K.heic'));
//
//        $startRequest = $muReflection->getMethod('startRequest');
//        $startRequest->setAccessible(true);
//        /** @var MultipartStartResponse $result */
//        $result = $startRequest->invokeArgs($mu, array($data));
//
//        $this->assertInstanceOf('Uploadcare\\DataClass\\MultipartStartResponse', $result);
//        $this->assertNotEmpty($result->getUuid());
//        $this->assertNotEmpty($result->getParts());
//
//        $parts = $result->getParts();
//        $this->assertInstanceOf('Uploadcare\\DataClass\\MultipartPreSignedUrl', $parts[0]);
    }
}
