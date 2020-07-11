<?php

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
use Uploadcare\Interfaces\UploadedFileInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;
use Uploadcare\Uploader;

class UploaderMethodsTest extends TestCase
{
    /**
     * @param ClientInterface $client
     *
     * @return Configuration
     */
    protected function makeConfiguration(ClientInterface $client)
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
    protected function makeClient($response)
    {
        $fileResponse = new Response(200, ['Content-Type' => 'application/json'], \file_get_contents(\dirname(__DIR__) . '/_data/uploaded-file.json'));
        $handler = new MockHandler([$response, $fileResponse]);

        return new Client(['handler' => HandlerStack::create($handler)]);
    }

    /**
     * @param array $responseBody
     *
     * @return Uploader
     */
    protected function makeUploaderWithResponse(array $responseBody)
    {
        $response = new Response(200, ['Content-Type' => 'application/json'], \json_encode($responseBody));
        $client = $this->makeClient($response);
        $config = $this->makeConfiguration($client);

        return new Uploader($config);
    }

    public function testSuccessDirectUpload()
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);
        $directUpload = (new \ReflectionObject($uploader))->getMethod('directUpload');
        $directUpload->setAccessible(true);
        $handle = \fopen(\dirname(__DIR__) . '/_data/test.jpg', 'rb');

        /** @var ResponseInterface $result */
        $result = $directUpload->invokeArgs($uploader, [$handle]);
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $responseContent = $result->getBody()->getContents();
        $this->assertNotEmpty($responseContent);

        $this->assertEquals($body, \json_decode($responseContent, true));
    }

    public function testFromResourceMethod()
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);

        $handle = \fopen(\dirname(__DIR__) . '/_data/test.jpg', 'rb');
        $result = $uploader->fromResource($handle);

        $this->assertInstanceOf(UploadedFileInterface::class, $result);
    }

    public function testFromPathMethod()
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);
        $result = $uploader->fromPath(\dirname(__DIR__) . '/_data/test.jpg');

        $this->assertInstanceOf(UploadedFileInterface::class, $result);
    }

    public function testFromContentMethod()
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);
        $content = \file_get_contents(\dirname(__DIR__) . '/_data/test.jpg');
        $result = $uploader->fromContent($content);

        $this->assertInstanceOf(UploadedFileInterface::class, $result);
    }

    public function testFromUrl()
    {
        $body = ['file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);
        $result = $uploader->fromUrl('https://httpbin.org/image/jpeg');

        $this->assertInstanceOf(UploadedFileInterface::class, $result);
    }

    public function testIfResponseSuccessButWrong()
    {
        $this->expectException(\RuntimeException::class);

        $body = ['not-a-file' => \uuid_create()];
        $uploader = $this->makeUploaderWithResponse($body);

        $handle = \fopen(\dirname(__DIR__) . '/_data/test.jpg', 'rb');
        $uploader->fromResource($handle);

        $this->expectExceptionMessageRegExp('Call to support');
    }
}
