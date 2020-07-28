<?php

namespace Tests\Decorated;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\FileApi;
use Uploadcare\Configuration;
use Uploadcare\File\File;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class DecoratedFileTest extends TestCase
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

    protected function fakeApi()
    {
        $handler = new MockHandler([
            new Response(200, [], stream_for(DataFile::fopen('file-info.json', 'rb'))),
        ]);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration(
            'public-key',
            new Signature('private-key'),
            $client,
            $this->serializer
        );

        return new FileApi($config);
    }

    /** @noinspection PhpParamsInspection */
    public function testCallInnerMethod()
    {
        $fileInfo = $this->serializer->deserialize(DataFile::contents('file-info.json'), File::class);
        self::assertInstanceOf(FileInfoInterface::class, $fileInfo);

        $decoratedFile = new \Uploadcare\File($fileInfo, $this->fakeApi());
        self::assertInstanceOf(FileInfoInterface::class, $decoratedFile->store());
    }
}
