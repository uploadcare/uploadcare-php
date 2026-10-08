<?php declare(strict_types=1);

namespace Tests\Uploader;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
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

    public function provideFileContentTypes(): array
    {
        return [
            'explicit mime type' => ['image/png', 'image/png'],
            'inferred from filename' => [null, 'text/plain'],
        ];
    }

    /**
     * @dataProvider provideFileContentTypes
     */
    public function testFileContentTypeHeader(?string $mimeType, string $expected): void
    {
        $requestBody = '';
        $boundary = '';
        // The uploader closes the file handle after sending, so the body has to be read at send time.
        $handler = new MockHandler([
            static function (RequestInterface $request) use (&$requestBody, &$boundary): ResponseInterface {
                $requestBody = (string) $request->getBody();
                \preg_match('/boundary=([^\s;]+)/', $request->getHeaderLine('Content-Type'), $matches);
                $boundary = $matches[1];

                return new Response(200, [], \json_encode(['file' => \uuid_create()]));
            },
            new Response(200, [], \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json')),
        ]);
        $uploader = new Uploader($this->makeConfiguration(new Client(['handler' => HandlerStack::create($handler)])));
        $uploader->fromContent('content', $mimeType, 'file.txt');

        $fileHeaders = null;
        foreach (\explode('--' . $boundary, $requestBody) as $part) {
            if (\strpos($part, 'name="file"') !== false) {
                $fileHeaders = \explode("\r\n\r\n", $part, 2)[0];
            }
        }

        self::assertNotNull($fileHeaders);
        self::assertSame(1, \substr_count(\strtolower($fileHeaders), 'content-type:'));
        self::assertStringContainsString("Content-Type: {$expected}\r\n", $fileHeaders . "\r\n");
    }
}
