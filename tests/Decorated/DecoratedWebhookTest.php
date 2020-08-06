<?php

namespace Tests\Decorated;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Api;
use Uploadcare\Apis\WebhookApi;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\WebhookResponse;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;
use Uploadcare\Webhook;
use Uploadcare\WebhookCollection;

class DecoratedWebhookTest extends TestCase
{
    protected function fakeApi($responses = [])
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, SerializerFactory::create());

        return new WebhookApi($config);
    }

    public function commonMethods()
    {
        return [
            ['getId'],
            ['getCreated'],
            ['getUpdated'],
            ['getEvent'],
            ['getTargetUrl'],
            ['getProject'],
            ['isActive'],
        ];
    }

    /**
     * @dataProvider commonMethods
     *
     * @param $method
     *
     * @throws \ReflectionException
     */
    public function testCommonMethods($method)
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('webhook-response.json')),
        ]);
        $wh = $api->createWebhook('https://localhost');
        $innerProperty = (new \ReflectionObject($wh))->getProperty('inner');
        $innerProperty->setAccessible(true);
        $inner = $innerProperty->getValue($wh);

        self::assertSame($inner->{$method}(), $wh->{$method}());
    }

    public function testCreateFromMethod()
    {
        $wh = SerializerFactory::create()->deserialize(DataFile::contents('webhook-response.json'), WebhookResponse::class);
        $collection = new WebhookCollection(new \Uploadcare\Response\WebhookCollection([$wh]), $this->fakeApi());
        $createFrom = (new \ReflectionObject($collection))->getMethod('createFrom');
        $createFrom->setAccessible(true);

        $result = $createFrom->invokeArgs($collection, [[$wh]]);
        self::assertEquals($collection, $result);
    }

    public function testElementClass()
    {
        self::assertEquals(Webhook::class, WebhookCollection::elementClass());
    }

    public function testDeleteMethod()
    {
        $api = $this->fakeApi([
            new Response(204),
        ]);
        $wh = $this->createMock(WebhookInterface::class);
        $wh->method('getTargetUrl')->willReturn('https://localhost.com/wh');

        $decorated = new Webhook($wh, $api);
        self::assertTrue($decorated->delete());
    }

    public function provideDecoratedMethods()
    {
        return [
            ['activate'],
            ['deactivate'],
            ['updateUrl'],
        ];
    }

    /**
     * @dataProvider provideDecoratedMethods
     *
     * @param string $method
     */
    public function testDecoratedMethods($method)
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('webhook-response.json')),
        ]);
        $wh = $this->createMock(WebhookInterface::class);
        $wh->method('getId')->willReturn(\uuid_create());

        $decorated = new Webhook($wh, $api);
        $result = $decorated->{$method}('https://new-url.com');
        self::assertInstanceOf(Webhook::class, $result);
    }

    public function testMainApiMethod()
    {
        $api = new Api(Configuration::create('public', 'private'));
        self::assertInstanceOf(WebhookApiInterface::class, $api->webhook());
    }
}
