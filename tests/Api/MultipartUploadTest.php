<?php

namespace Tests\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Configuration;
use Uploadcare\Exception\HttpException;
use Uploadcare\Exception\Upload\RequestParametersException;
use Uploadcare\MultipartResponse\MultipartStartResponse;
use Uploadcare\Uploader\Uploader;

class MultipartUploadTest extends TestCase
{
    protected function getConfiguration(): Configuration
    {
        return Configuration::create('public-key', 'private-key');
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Uploader
     */
    protected function getMockUploader(array $methods = [])
    {
        return $this->getMockBuilder(Uploader::class)
            ->setConstructorArgs([$this->getConfiguration()])
            ->setMethods($methods)
            ->getMock();
    }

    public function testStartUploadMethod(): void
    {
        $response = new Response(200, [], Utils::streamFor(DataFile::contents('startResponse.json')));
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

    public function testResponseExceptionInStartUpload(): void
    {
        $exception = new ClientException('Wrong request', new Request('POST', 'uri'), new Response(400, [], 'Wrong request'));

        $uploader = $this->getMockUploader(['sendRequest']);
        $uploader
            ->expects(self::once())
            ->method('sendRequest')
            ->willThrowException($exception);

        $startUpload = (new \ReflectionObject($uploader))->getMethod('startUpload');
        $startUpload->setAccessible(true);

        $this->expectException(RequestParametersException::class);
        $startUpload->invokeArgs($uploader, [100, 'text/html', 'no-name', 'auto']);
        $this->expectExceptionMessageRegExp('Wrong request');
    }

    public function testExceptionInUploadPartsMethod(): void
    {
        $exception = new ClientException('Wrong request', new Request('POST', 'https://some-middleware-endpoint'), new Response(400));

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

        $this->expectException(RequestParametersException::class);
        $uploadParts->invokeArgs($uploader, [$mr, $handle]);
        $this->expectExceptionMessageRegExp('Bad request');
    }

    public function testExceptionInFinishUpload(): void
    {
        $exception = new TooManyRedirectsException('Too many redirects', new Request('POST', 'https://final-endpoint'), new Response(400));

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
