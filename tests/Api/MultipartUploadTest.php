<?php

namespace Tests\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Configuration;
use Uploadcare\Exception\HttpException;
use Uploadcare\MultipartResponse\MultipartStartResponse;
use Uploadcare\Uploader\Uploader;

class MultipartUploadTest extends TestCase
{
    protected function getConfiguration()
    {
        return Configuration::create('public-key', 'private-key');
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Uploader
     */
    protected function getMockUploader($methods = [])
    {
        return $this->getMockBuilder(Uploader::class)
            ->setConstructorArgs([$this->getConfiguration()])
            ->setMethods($methods)
            ->getMock();
    }

    public function testStartUploadMethod()
    {
        $response = new Response(200, [], stream_for(DataFile::contents('startResponse.json')));
        $uploader = $this->getMockUploader(['sendRequest']);
        $uploader
            ->expects(self::once())
            ->method('sendRequest')
            ->willReturn($response);

        $startUpload = (new \ReflectionObject($uploader))->getMethod('startUpload');
        $startUpload->setAccessible(true);

        $result = $startUpload->invokeArgs($uploader, [100, 'text/html', 'no-name', 'auto']);
        self::assertInstanceOf(MultipartStartResponse::class, $result);
    }

    public function testResponseExceptionInStartUpload()
    {
        $exception = new ClientException('Wrong request', new Request('POST', 'uri'));
        $uploader = $this->getMockUploader(['sendRequest']);
        $uploader
            ->expects(self::once())
            ->method('sendRequest')
            ->willThrowException($exception);

        $startUpload = (new \ReflectionObject($uploader))->getMethod('startUpload');
        $startUpload->setAccessible(true);

        $this->expectException(HttpException::class);
        $startUpload->invokeArgs($uploader, [100, 'text/html', 'no-name', 'auto']);
        $this->expectExceptionMessageRegExp('Wrong request');
    }

    public function testExceptionInUploadPartsMethod()
    {
        $exception = new ClientException('Wrong request', new Request('POST', 'https://some-middleware-endpoint'));
        $uploader = $this->getMockUploader(['sendRequest']);
        $uploader
            ->expects(self::once())
            ->method('sendRequest')
            ->willThrowException($exception);

        $mr = (new MultipartStartResponse())
            ->addPart('https://some-middleware-endpoint');
        $handle = DataFile::fopen('test.jpg', 'rb');
        $uploadParts = (new \ReflectionObject($uploader))->getMethod('uploadParts');
        $uploadParts->setAccessible(true);

        $this->expectException(HttpException::class);
        $uploadParts->invokeArgs($uploader, [$mr, $handle]);
        $this->expectExceptionMessageRegExp('some-middleware-endpoint');
    }

    public function testExceptionInFinishUpload()
    {
        $exception = new TooManyRedirectsException('Too many redirects', new Request('POST', 'https://final-endpoint'));
        $uploader = $this->getMockUploader(['sendRequest']);
        $uploader
            ->expects(self::once())
            ->method('sendRequest')
            ->willThrowException($exception);
        $mr = (new MultipartStartResponse())
            ->setUuid(\uuid_create());

        $finishUpload = (new \ReflectionObject($uploader))->getMethod('finishUpload');
        $finishUpload->setAccessible(true);

        $this->expectException(HttpException::class);
        $finishUpload->invokeArgs($uploader, [$mr]);
        $this->expectExceptionMessageRegExp('Unable to finish multipart-upload request');
    }
}
