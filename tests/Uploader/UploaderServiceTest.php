<?php declare(strict_types=1);

namespace Tests\Uploader;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Uploadcare\Configuration;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Interfaces\UploaderInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;
use Uploadcare\Uploader\Uploader;

/**
 * @group broken
 */
class UploaderServiceTest extends TestCase
{
    private function getMockUploader(?Configuration $configuration = null): UploaderInterface
    {
        $uploader = $this->getMockBuilder(Uploader::class)
            ->setConstructorArgs([$configuration ?: $this->getConf()])
            ->getMock();

        $response = $this->getMockBuilder(FileInfoInterface::class);
        $uploader->expects(self::any())->method('fromResource')
            ->willReturn($response);

        return $uploader;
    }

    private function getConf(?ClientInterface $client = null): ConfigurationInterface
    {
        return new Configuration('demo-public-key', new Signature('demo-private-key'), $client ?: $this->mockClient(), $this->getSerializer());
    }

    /**
     * @param int  $status
     * @param null $body
     */
    private function mockClient($status = 200, array $headers = [], $body = null): ClientInterface
    {
        $mock = new MockHandler([
            new Response($status, $headers, $body),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    private function getSerializer(): SerializerInterface
    {
        return SerializerFactory::create();
    }

    public function testCheckResourceException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $uploader = new Uploader($this->getConf());
        $checkResource = (new \ReflectionObject($uploader))->getMethod('checkResource');
        $checkResource->setAccessible(true);
        $checkResource->invokeArgs($uploader, ['string']);

        $this->expectExceptionMessageMatches('/Expected resource/');
    }

    public function testCheckExistsButNotValidResource(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $uploader = new Uploader($this->getConf());
        $checkResource = (new \ReflectionObject($uploader))->getMethod('checkResource');
        $checkResource->setAccessible(true);
        $checkResource->invokeArgs($uploader, [\fopen(\dirname(__DIR__) . '/_data/empty.file.txt', 'wb')]);

        $this->expectExceptionMessageMatches('/metadata parameter can be/');
    }

    public function testCheckResourceMetadataNotSet(): void
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

        $this->expectExceptionMessageMatches('/not exists in metadata/');
    }

    public function testCheckInvalidMetadataSet(): void
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

        $this->expectExceptionMessageMatches('/metadata parameter can be/');
    }

    public function testMakeMultipartParameters(): void
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
        self::assertArrayHasKey('multipart', $result);
        foreach ($result['multipart'] as $item) {
            self::assertArrayHasKey('name', $item);
            self::assertArrayHasKey('contents', $item);
        }
    }

    public function testUploadFromFileIfFileNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $uploader = new Uploader($this->getConf());
        $uploader->fromPath('/file/does/not/exists');
        $this->expectExceptionMessageMatches('/Unable to read/');
    }

    /**
     * @group local-only
     *
     * @throws \ReflectionException
     */
    public function testGetSizeMethod(): void
    {
        $path = \dirname(__DIR__) . '/_data/file-info.json';
        $size = \filesize($path);
        $handle = \fopen($path, 'rb');

        $uploader = new Uploader($this->getConf());
        $getSize = (new \ReflectionObject($uploader))->getMethod('getSize');
        $getSize->setAccessible(true);

        self::assertEquals($size, $getSize->invokeArgs($uploader, [$handle]));
        self::assertEquals(0, $getSize->invokeArgs($uploader, [\fopen('https://httpbin.org/encoding/utf8', 'rb')]));
    }
}
