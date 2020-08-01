<?php

namespace Tests\Decorated;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Tests\DataFile;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Configuration;
use Uploadcare\File\File;
use Uploadcare\GroupCollection;
use Uploadcare\Group;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class DecoratedGroupTest extends TestCase
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

    protected function fakeApi($responses = [])
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, $this->serializer);

        return new GroupApi($config);
    }

    public function testGroupInfo()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('group/group-info-response.json')),
        ]);
        self::assertInstanceOf(Group::class, $api->groupInfo(\uuid_create()));
    }

    public function testStoreGroup()
    {
        $api = $this->fakeApi([
            new Response(200),
            new Response(200, [], DataFile::contents('group/group-info-response.json')),
        ]);
        self::assertInstanceOf(Group::class, $api->storeGroup(\uuid_create()));
    }

    public function provideMethods()
    {
        return [
            ['getId'],
            ['getDatetimeCreated'],
            ['getDatetimeStored'],
            ['getFilesCount'],
            ['getCdnUrl'],
            ['getUrl'],
        ];
    }

    /**
     * @dataProvider provideMethods
     * @param string $method
     * @throws \ReflectionException
     */
    public function testGroupMethods($method)
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('group/group-info-response.json')),
        ]);
        $group = $api->groupInfo(\uuid_create());
        $innerProperty = (new ReflectionObject($group))->getProperty('inner');
        $innerProperty->setAccessible(true);
        $inner = $innerProperty->getValue($group);

        self::assertSame($inner->{$method}(), $group->{$method}());
    }

    public function testCreateFromElements()
    {
        $serializer = SerializerFactory::create();
        $file = $serializer->deserialize(DataFile::contents('file-info.json'), File::class);
        /** @noinspection PhpParamsInspection */
        $group = (new \Uploadcare\File\Group())->addFile($file);

        $collection = new GroupCollection(new \Uploadcare\File\GroupCollection(), $this->fakeApi());
        $createFrom = (new ReflectionObject($collection))->getMethod('createFrom');
        $createFrom->setAccessible(true);

        $result = $createFrom->invokeArgs($collection, [[$group]]);
        self::assertInstanceOf(GroupCollection::class, $result);
        self::assertInstanceOf(Group::class, $result->first());
    }

    public function testElementClass()
    {
        self::assertEquals(Group::class, GroupCollection::elementClass());
    }
}
