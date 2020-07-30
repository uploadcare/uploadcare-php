<?php

namespace Tests\Decorated;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Configuration;
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
}
