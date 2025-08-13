<?php declare(strict_types=1);

namespace Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Uploadcare\Apis\FileApi;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Response\FileListResponse;

class FileApiAnswersTest extends TestCase
{
    protected function fileContents(string $path): string
    {
        $filePath = \sprintf('%s/%s', \dirname(__DIR__) . '/_data', \ltrim($path, '/'));
        if (!\is_file($filePath)) {
            throw new \RuntimeException(\sprintf('Cannot load %s file', $filePath));
        }

        return \file_get_contents($filePath);
    }

    protected function getClient(string $path, int $status = 200): ClientInterface
    {
        $dataDir = \dirname(__DIR__) . '/_data';
        $filePath = \sprintf('%s/%s', $dataDir, \ltrim($path, '/'));
        if (!\is_file($filePath)) {
            throw new \RuntimeException(\sprintf('Cannot load %s file', $filePath));
        }

        $mock = new MockHandler([
            new Response($status, [
                'Content-Type' => \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION),
                'Access-Control-Allow-Origin' => 'https://uploadcare.com',
            ], \file_get_contents($filePath)),
        ]);

        $stack = HandlerStack::create($mock);

        return new Client(['handler' => $stack]);
    }

    public function getConfig(?ClientInterface $client = null): ConfigurationInterface
    {
        $configuration = Configuration::create('demo-public-key', 'demo-private-key');
        if ($client === null) {
            return $configuration;
        }

        $clientProperty = (new \ReflectionObject($configuration))->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($configuration, $client);

        return $configuration;
    }

    public function testListFiles(): void
    {
        $data = \json_decode($this->fileContents('file-list-api-response.json'), true);
        $client = $this->getClient('file-list-api-response.json');
        $result = (new FileApi($this->getConfig($client)))->listFiles();

        self::assertInstanceOf(ListResponseInterface::class, $result);
        self::assertEmpty($result->getNext());
        self::assertCount(\count($data['results']), $result->getResults());
        self::assertInstanceOf(FileInfoInterface::class, $result->getResults()->first());
    }

    public function testStoreFile(): void
    {
        $client = $this->getClient('store-file-api-response.json');
        $result = (new FileApi($this->getConfig($client)))->storeFile('3c269810-c17b-4e2c-92b6-25622464d866');

        self::assertInstanceOf(FileInfoInterface::class, $result);
    }

    public function testDeleteFile(): void
    {
        $client = $this->getClient('delete-file-api-response.json');
        $result = (new FileApi($this->getConfig($client)))->deleteFile('3c269810-c17b-4e2c-92b6-25622464d866');

        self::assertInstanceOf(FileInfoInterface::class, $result);
    }

    public function testFileInfo(): void
    {
        $client = $this->getClient('file-info-api-response.json');
        $result = (new FileApi($this->getConfig($client)))->fileInfo('03ccf9ab-f266-43fb-973d-a6529c55c2ae');

        self::assertInstanceOf(FileInfoInterface::class, $result);
    }

    public function testBatchStoreFile(): void
    {
        $client = $this->getClient('batch-store-file-api-response.json');
        $ids = [
            '21975c81-7f57-4c7a-aef9-acfe28779f78',
            'cbaf2d73-5169-4b2b-a543-496cf2813dff',
        ];
        $result = (new FileApi($this->getConfig($client)))->batchStoreFile($ids);

        self::assertInstanceOf(BatchResponseInterface::class, $result);
        self::assertInstanceOf(ResponseProblemInterface::class, $result->getProblems()[0]);
        self::assertInstanceOf(CollectionInterface::class, $result->getResult());
        self::assertInstanceOf(FileInfoInterface::class, $result->getResult()->first());
    }

    public function testBatchDeleteFile(): void
    {
        $client = $this->getClient('batch-delete-file-api-response.json');
        $ids = [
            '21975c81-7f57-4c7a-aef9-acfe28779f78',
            'cbaf2d73-5169-4b2b-a543-496cf2813dff',
        ];
        $result = (new FileApi($this->getConfig($client)))->batchDeleteFile($ids);

        self::assertInstanceOf(BatchResponseInterface::class, $result);
        self::assertInstanceOf(ResponseProblemInterface::class, $result->getProblems()[0]);
        self::assertInstanceOf(CollectionInterface::class, $result->getResult());
        self::assertInstanceOf(FileInfoInterface::class, $result->getResult()->first());
    }

    public function testCopyToLocalStorage(): void
    {
        $client = $this->getClient('copy-to-local-storage-api-response.json');
        $result = (new FileApi($this->getConfig($client)))->copyToLocalStorage('03ccf9ab-f266-43fb-973d-a6529c55c2ae', true);

        self::assertInstanceOf(FileInfoInterface::class, $result);
    }

    public function testCopyToRemoteStorage(): void
    {
        $source = \json_decode($this->fileContents('copy-to-remote-storage-api-response.json'), true);
        $client = $this->getClient('copy-to-remote-storage-api-response.json', 201);
        $result = (new FileApi($this->getConfig($client)))->copyToRemoteStorage('03ccf9ab-f266-43fb-973d-a6529c55c2ae', 'my-target');

        self::assertEquals($source['result'], $result);
    }

    public function testApiNextPageNotNull(): void
    {
        $headers = [
            'Content-Type' => \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION),
            'Access-Control-Allow-Origin' => 'https://uploadcare.com',
        ];
        $content = $this->fileContents('file-list-api-response.json');

        $firstResponse = new Response(200, $headers, $content);
        $nextResponse = new Response(200, $headers, $content);

        $handler = new MockHandler([$firstResponse, $nextResponse]);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = $this->getConfig($client);

        $api = new FileApi($config);
        $firstPage = $api->listFiles();
        self::assertInstanceOf(FileListResponse::class, $firstPage);
        self::assertEmpty($firstPage->getNext());
    }

    public function testApiNextPageIsNull(): void
    {
        $source = \json_decode($this->fileContents('file-list-api-response.json'), true);
        $source['next'] = null;
        $response = new Response(200, [
            'Content-Type' => \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION),
            'Access-Control-Allow-Origin' => 'https://uploadcare.com',
        ], \json_encode($source));
        $handler = new MockHandler([$response]);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = $this->getConfig($client);
        $api = new FileApi($config);
        $firstPage = $api->listFiles();
        self::assertInstanceOf(FileListResponse::class, $firstPage);
        self::assertEmpty($firstPage->getNext());
        $nextPage = $api->nextPage($firstPage);
        self::assertNull($nextPage);
    }
}
