<?php declare(strict_types=1);

namespace Tests\Decorated;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Configuration;
use Uploadcare\File\File;
use Uploadcare\Group;
use Uploadcare\GroupCollection;
use Uploadcare\Interfaces\Api\GroupApiInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class DecoratedGroupTest extends TestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = SerializerFactory::create();
    }

    protected function fakeApi(array $responses = []): GroupApiInterface
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, $this->serializer);

        return new GroupApi($config);
    }

    public function testGroupInfo(): void
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('group/group-info-response.json')),
        ]);
        self::assertInstanceOf(Group::class, $api->groupInfo(\uuid_create()));
    }

    public function provideMethods(): array
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
     *
     * @throws \ReflectionException
     */
    public function testGroupMethods(string $method): void
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('group/group-info-response.json')),
        ]);
        $group = $api->groupInfo(\uuid_create());
        $innerProperty = (new \ReflectionObject($group))->getProperty('inner');
        $innerProperty->setAccessible(true);
        $inner = $innerProperty->getValue($group);

        self::assertSame($inner->{$method}(), $group->{$method}());
    }

    public function testCreateFromElements(): void
    {
        $serializer = SerializerFactory::create();
        $file = $serializer->deserialize(DataFile::contents('file-info.json'), File::class);
        /** @noinspection PhpParamsInspection */
        $group = (new \Uploadcare\File\Group())->addFile($file);

        $collection = new GroupCollection(new \Uploadcare\File\GroupCollection(), $this->fakeApi());
        $createFrom = (new \ReflectionObject($collection))->getMethod('createFrom');
        $createFrom->setAccessible(true);

        $result = $createFrom->invokeArgs($collection, [[$group]]);
        self::assertInstanceOf(GroupCollection::class, $result);
        self::assertInstanceOf(Group::class, $result->first());
    }

    public function testElementClass(): void
    {
        self::assertEquals(Group::class, GroupCollection::elementClass());
    }
}
