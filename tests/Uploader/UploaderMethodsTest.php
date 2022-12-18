<?php declare(strict_types=1);

namespace Tests\Uploader;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\UploaderInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;
use Uploadcare\Uploader\Uploader;

class UploaderMethodsTest extends TestCase
{
    /**
     * @return Configuration
     */
    protected function makeConfiguration(ClientInterface $client): ConfigurationInterface
    {
        $sign = new Signature('demo-private-key');
        $serializer = new Serializer(new SnackCaseConverter());

        return new Configuration('demo-public-key', $sign, $client, $serializer);
    }

    /**
     * @param ResponseInterface|GuzzleException $response
     *
     * @return Client
     */
    protected function makeClient($response): ClientInterface
    {
        $fileResponse = new Response(200, ['Content-Type' => 'application/json'], \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json'));
        $handler = new MockHandler([$response, $fileResponse]);

        return new Client(['handler' => HandlerStack::create($handler)]);
    }

    /**
     * @return Uploader
     */
    protected function makeUploaderWithResponse(array $responseBody): UploaderInterface
    {
        $response = new Response(200, ['Content-Type' => 'application/json'], \json_encode($responseBody));
        $client = $this->makeClient($response);
        $config = $this->makeConfiguration($client);

        return new Uploader($config);
    }

    public function testFromPathMethod(): void
    {
        $path = \dirname(__DIR__) . '/_data/test.jpg';
        $body = ['file' => \uuid_create()];

        $uploader = $this->makeUploaderWithResponse($body);
        $response = $uploader->fromPath($path, null, null, 'auto', ['foo' => 'bar']);
        self::assertInstanceOf(FileInfoInterface::class, $response);
    }

    public function testFromResourceMethod(): void
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);

        $handle = \fopen(\dirname(__DIR__) . '/_data/test.jpg', 'rb');
        self::assertInstanceOf(FileInfoInterface::class, $uploader->fromResource($handle));
    }

    public function testFromContentMethod(): void
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);
        $content = \file_get_contents(\dirname(__DIR__) . '/_data/test.jpg');

        self::assertInstanceOf(FileInfoInterface::class, $uploader->fromContent($content));
    }
}
