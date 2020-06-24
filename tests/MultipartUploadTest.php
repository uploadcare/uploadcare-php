<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Uploadcare\AbstractUploader;
use Uploadcare\Api;
use Uploadcare\DataClass\MultipartStartResponse;
use Uploadcare\MultipartUpload;
use Uploadcare\Signature\SecureSignature;
use Uploadcare\Uploader;
use Uploadcare\Uuid;

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

    /**
     * @param mixed $response
     * @return \PHPUnit_Framework_MockObject_MockObject|Uploader
     */
    protected function getMockUploader($response)
    {
        $uploader = $this
            ->getMockBuilder('Uploadcare\\MultipartUpload')
            ->setConstructorArgs(array($this->api))
            ->setMethods(array('runRequest'))
            ->getMock();
        $uploader->method('runRequest')
            ->willReturn($response);

        return $uploader;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Uploader
     */
    protected function getCurlFromMockUploader()
    {
        $uploader = $this
            ->getMockBuilder('Uploadcare\\MultipartUpload')
            ->setConstructorArgs(array($this->api))
            ->setMethods(array('runRequest'))
            ->getMock();
        $uploader->method('runRequest')
            ->willReturnArgument(0);

        return $uploader;
    }

    protected function getRealUploader()
    {
        return new MultipartUpload($this->api, new SecureSignature(UC_SECRET_KEY, 3600));
    }

    /**
     * @param string $path
     * @param string $mimeType
     * @param string $name
     * @throws \ReflectionException
     * @return array
     */
    protected function getStartUploadParams($path, $mimeType, $name)
    {
        $mu = $this->getRealUploader();
        $muReflection = new ReflectionObject($mu);
        $startRequestData = $muReflection->getMethod('extendRequestData');
        $startRequestData->setAccessible(true);

        return $startRequestData->invokeArgs($mu, array(\filesize($path), $mimeType, $name));
    }

    public function testStartRequestDataMethod()
    {
        $path = __DIR__ . '/test.jpg';
        $mimeType = 'image/jpeg';

        $result = $this->getStartUploadParams($path, $mimeType, 'test.jpg');
        $this->assertArrayHasKey('filename', $result);
        $this->assertEquals('test.jpg', $result['filename']);
        $this->assertArrayHasKey(Uploader::UPLOADCARE_PUB_KEY_KEY, $result);
    }

    public function testStartRequestWithNormalData()
    {
        $validResponse = \file_get_contents(__DIR__ . '/data/startResponse.json');
        $uploader = $this->getMockUploader(json_decode($validResponse));

        $path = __DIR__ . '/test.jpg';
        $mimeType = 'image/jpeg';
        $muReflection = new ReflectionObject($uploader);
        $startRequestData = $muReflection->getMethod('extendRequestData');
        $startRequestData->setAccessible(true);
        $data = $startRequestData->invokeArgs($uploader, array(\filesize($path), $mimeType, 'test.jpg'));

        $startRequest = $muReflection->getMethod('startRequest');
        $startRequest->setAccessible(true);
        /** @var MultipartStartResponse $result */
        $result = $startRequest->invokeArgs($uploader, array($data));

        $this->assertInstanceOf('Uploadcare\\DataClass\\MultipartStartResponse', $result);
        $this->assertNotEmpty($result->getUuid());
        $this->assertNotEmpty($result->getParts());

        $parts = $result->getParts();
        $this->assertInstanceOf('Uploadcare\\DataClass\\MultipartPreSignedUrl', $parts[0]);
    }

    public function testInspectRequestParameters()
    {
        $mu = $this->getCurlFromMockUploader();
        $data = $this->getStartUploadParams(__DIR__ . '/test.jpg', 'image/jpeg', 'test.jpeg');
        $data[AbstractUploader::UPLOADCARE_STORE_KEY] = 'auto';

        $muReflection = new ReflectionObject($mu);
        $startRequest = $muReflection->getMethod('startRequest');
        $startRequest->setAccessible(true);

        $result = $startRequest->invokeArgs($mu, array($data, true));
        $this->assertTrue(\is_resource($result));

        \curl_setopt($result, CURLOPT_URL, 'https://httpbin.org/post');
        $response = \Uploadcare\jsonDecode(\curl_exec($result), true);

        $this->assertArrayHasKey('form', $response);
        $formData = $response['form'];
        $this->assertArrayHasKey(Uploader::UPLOADCARE_PUB_KEY_KEY, $formData);
        $this->assertArrayHasKey(Uploader::UPLOADCARE_STORE_KEY, $formData);

        $this->assertArrayHasKey('headers', $response);
        $headersData = $response['headers'];
        $this->assertArrayHasKey('Content-Type', $headersData);
        $this->assertStringStartsWith('multipart/form-data; boundary=', $headersData['Content-Type']);
    }

    public function testFinishUploadMethod()
    {
        $response = MultipartStartResponse::create((object) array(
            'uuid' => Uuid::create(),
            'parts' => array('https://example.com'),
        ));
        $mu = $this->getCurlFromMockUploader();

        $muReflection = new ReflectionObject($mu);
        $finishUpload = $muReflection->getMethod('finishUpload');
        $finishUpload->setAccessible(true);

        $result = $finishUpload->invokeArgs($mu, array($response));
        $this->assertTrue(\is_resource($result));
        \curl_setopt($result, CURLOPT_URL, 'https://httpbin.org/post');
        $response = \Uploadcare\jsonDecode(\curl_exec($result), true);

        $this->assertArrayHasKey('form', $response);
        $formData = $response['form'];
        $this->assertArrayHasKey(Uploader::UPLOADCARE_PUB_KEY_KEY, $formData);

        $this->assertArrayHasKey('headers', $response);
        $headersData = $response['headers'];
        $this->assertArrayHasKey('Content-Type', $headersData);
        $this->assertStringStartsWith('multipart/form-data; boundary=', $headersData['Content-Type']);
    }

    public function testUploadPartsWithNoFile()
    {
        $this->setExpectedException('RuntimeException');

        $response = MultipartStartResponse::create((object) array(
            'uuid' => Uuid::create(),
            'parts' => array('https://example.com'),
        ));
        $mu = $this->getCurlFromMockUploader();
        $muReflection = new ReflectionObject($mu);

        $uploadParts = $muReflection->getMethod('uploadParts');
        $uploadParts->setAccessible(true);
        $uploadParts->invokeArgs($mu, array($response, '/file/does/not/exists'));
    }

    public function testCallsInUploaderParts()
    {
        $response = MultipartStartResponse::create((object) array(
            'uuid' => Uuid::create(),
            'parts' => array('https://example.com'),
        ));
        $mu = $this->getCurlFromMockUploader();
        $mu->expects($this->once())
            ->method('runRequest');

        $muReflection = new ReflectionObject($mu);

        $uploadParts = $muReflection->getMethod('uploadParts');
        $uploadParts->setAccessible(true);
        $uploadParts->invokeArgs($mu, array($response, __DIR__ . '/test.jpg'));
    }

    public function testUploaderMultipartUploadWithNoFile()
    {
        $this->setExpectedException('RuntimeException');
        $mu = new MultipartUpload($this->api);
        $mu->fromPath('/file/does/not/exists');
    }
}
