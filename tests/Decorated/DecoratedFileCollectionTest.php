<?php

namespace Tests\Decorated;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\FileApi;
use Uploadcare\Configuration;
use Uploadcare\File;
use Uploadcare\FileCollection;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Response\BatchFileResponse;
use Uploadcare\Response\FileListResponse;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class DecoratedFileCollectionTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        parent::setUp();
        $this->serializer = SerializerFactory::create();
    }

    /**
     * @param array $responses
     *
     * @return FileApi
     */
    protected function fakeApi($responses = [])
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, $this->serializer);

        return new FileApi($config);
    }

    public function testListFiles()
    {
        $collectionResponse = new Response(200, [], DataFile::contents('file-list-api-response.json'));
        $batchStoreResponse = new Response(200, [], DataFile::contents('batch-store-file-api-response.json'));
        $api = $this->fakeApi([$collectionResponse, $batchStoreResponse]);
        $result = $api->listFiles();

        self::assertInstanceOf(FileListResponse::class, $result);
        self::assertInstanceOf(FileCollection::class, $result->getResults());
        self::assertInstanceOf(File::class, $result->getResults()->first());

        $store = $result->getResults()->store();
        self::assertInstanceOf(BatchFileResponse::class, $store);
        self::assertInstanceOf(File::class, $store->getResult()->first());
    }

    public function testBatchStoreFiles()
    {
        $responses = [
            $collectionResponse = new Response(200, [], DataFile::contents('file-list-api-response.json')),
            $batchStoreResponse = new Response(200, [], DataFile::contents('batch-delete-file-api-response.json')),
        ];
        $api = $this->fakeApi($responses);
        $result = $api->listFiles();

        $deleted = $result->getResults()->delete();
        self::assertInstanceOf(BatchFileResponse::class, $deleted);
        self::assertInstanceOf(File\File::class, $deleted->getResult()->first());
    }

    public function testCollectionCreateFrom()
    {
        $file = SerializerFactory::create()->deserialize(DataFile::contents('file-info.json'), File\File::class);
        $collection = new FileCollection(new File\FileCollection([$file]), $this->fakeApi());
        $createFrom = (new \ReflectionObject($collection))->getMethod('createFrom');
        $createFrom->setAccessible(true);

        self::assertEquals($collection, $createFrom->invokeArgs($collection, [[$file]]));
    }

    public function testElementClass()
    {
        self::assertEquals(File::class, FileCollection::elementClass());
    }
}
