<?php

namespace Tests\Uploader;

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Uploadcare\Configuration;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Security\Signature;
use Uploadcare\Uploader;

class UploaderServiceTest extends TestCase
{
    /**
     * @param Configuration|null $configuration
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Uploader
     */
    private function getMockUploader(Configuration $configuration = null)
    {
        $uploader = $this->getMockBuilder(Uploader::class)
            ->setConstructorArgs([$configuration ?: $this->getConf()])
            ->setMethods(['fromResource'])
            ->getMock();

        $uploader->method('fromResource')
            ->willReturnArgument(0);

        return $uploader;
    }

    private function getConf(ClientInterface $client = null)
    {
        return new Configuration('demo-public-key', new Signature('demo-private-key'), $client ?: $this->mockClient());
    }

    /**
     * @param int   $status
     * @param array $headers
     * @param null  $body
     *
     * @return ClientInterface
     */
    private function mockClient($status = 200, array $headers = [], $body = null)
    {
        $mock = new MockHandler([
            new Response($status, $headers, $body),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    public function testCheckResourceException()
    {
        $this->expectException(InvalidArgumentException::class);

        $uploader = new Uploader($this->getConf());
        $checkResource = (new \ReflectionObject($uploader))->getMethod('checkResource');
        $checkResource->setAccessible(true);
        $checkResource->invokeArgs($uploader, ['string']);

        $this->expectExceptionMessageRegExp('Expected resource');
    }

    public function testCheckExistsButNotValidResource()
    {
        $this->expectException(\UnexpectedValueException::class);
        $uploader = new Uploader($this->getConf());
        $checkResource = (new \ReflectionObject($uploader))->getMethod('checkResource');
        $checkResource->setAccessible(true);
        $checkResource->invokeArgs($uploader, [\fopen(\dirname(__DIR__) . '/_data/empty.file.txt', 'w+b')]);

        $this->expectExceptionMessageRegExp('metadata parameter can be');
    }

    public function testCheckResourceMetadataNotSet()
    {
        $this->expectException(\UnexpectedValueException::class);
        $uploader = new Uploader($this->getConf());
        $checkResourceMetadata = (new \ReflectionObject($uploader))->getMethod('checkResourceMetadata');
        $checkResourceMetadata->setAccessible(true);
        $metadata = [
            'foo' => 'bar',
            'wrapper_type' => 'some type',
        ];
        $checkResourceMetadata->invokeArgs($uploader, [$metadata]);

        $this->expectExceptionMessageRegExp('not exists in metadata');
    }

    public function testCheckInvalidMetadataSet()
    {
        $this->expectException(\UnexpectedValueException::class);
        $uploader = new Uploader($this->getConf());
        $checkResourceMetadata = (new \ReflectionObject($uploader))->getMethod('checkResourceMetadata');
        $checkResourceMetadata->setAccessible(true);

        $meta = [
            'wrapper_type' => 'not valid',
            'stream_type' => 'tcp_socket/ssl',
            'mode' => 'rb',
        ];
        $checkResourceMetadata->invokeArgs($uploader, [$meta]);

        $this->expectExceptionMessageRegExp('metadata parameter can be');
    }

    public function testMakeMultipartParameters()
    {
        $uploader = new Uploader($this->getConf());
        $reflection = new \ReflectionObject($uploader);
        $getDefaultParameters = $reflection->getMethod('getDefaultParameters');
        $getDefaultParameters->setAccessible(true);
        $defaults = $getDefaultParameters->invoke($uploader);

        $makeMultipartParameters = $reflection->getMethod('makeMultipartParameters');
        $makeMultipartParameters->setAccessible(true);

        $parameters = \array_merge($defaults, [
            ['name' => 'file', 'contents' => 'Hello, world'],
        ]);

        $result = $makeMultipartParameters->invokeArgs($uploader, [$parameters]);
        $this->assertArrayHasKey('multipart', $result);
        foreach ($result['multipart'] as $item) {
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('contents', $item);
        }
    }

    public function testUploadFromFileIfFileNotExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $uploader = $this->getMockUploader();
        $uploader->fromPath('/file/does/not/exists');
        $this->expectExceptionMessageRegExp('Unable to read');
    }

    public function testUploadFromExistingFile()
    {
        $path = \dirname(__DIR__) . '/_data/file-example.json';
        /** @var resource $result */
        $result = $this->getMockUploader()->fromPath($path);

        $this->assertTrue(\is_resource($result));
        $this->assertEquals($path, \stream_get_meta_data($result)['uri']);
    }

    public function testUploadFromNotExistsUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        $url = 'http://host.does.hot.exists';
        $this->getMockUploader()->fromUrl($url);
        $this->expectExceptionMessageRegExp('Unable to open');
    }

    public function testUploadFromValidUrl()
    {
        $url = 'https://httpbin.org/gzip';
        $this->assertTrue(\is_resource($this->getMockUploader()->fromUrl($url)));
    }

    public function testUploadFromContent()
    {
        $content = Factory::create()->realText();
        $this->assertTrue(\is_resource($this->getMockUploader()->fromContent($content)));
    }

    public function testGetSizeMethod()
    {
        $path = \dirname(__DIR__) . '/_data/file-example.json';
        $size = \filesize($path);
        $handle = \fopen($path, 'rb');

        $uploader = new Uploader($this->getConf());
        $getSize = (new \ReflectionObject($uploader))->getMethod('getSize');
        $getSize->setAccessible(true);

        $this->assertEquals($size, $getSize->invokeArgs($uploader, [$handle]));
        $this->assertEquals(0, $getSize->invokeArgs($uploader, [\fopen('https://httpbin.org/encoding/utf8', 'rb')]));
    }

    private function checkClientRequestArgument($num)
    {
        /** @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
        $client->method('request')
            ->willReturnArgument($num);
        $conf = $this->getConf($client);

        return $this->getMockUploader($conf);
    }

    public function testSendRequestMethodHeaders()
    {
        $uploader = $this->checkClientRequestArgument(2);

        $sendRequest = (new \ReflectionObject($uploader))->getMethod('sendRequest');
        $sendRequest->setAccessible(true);

        $args = ['GET', '/path/', ['foo' => 'bar']];
        $result = $sendRequest->invokeArgs($uploader, $args);

        $this->assertArrayHasKey('headers', $result);
    }

    public function testSendRequestMethodUri()
    {
        $uploader = $this->checkClientRequestArgument(1);
        $sendRequest = (new \ReflectionObject($uploader))->getMethod('sendRequest');
        $sendRequest->setAccessible(true);

        $args = ['GET', '/path/', ['foo' => 'bar']];
        $result = $sendRequest->invokeArgs($uploader, $args);

        $this->assertStringStartsWith('https://', $result);
    }
}
