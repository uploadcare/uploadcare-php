<?php

namespace Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\WebhookApi;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\WebhookCollection;
use Uploadcare\Response\WebhookResponse;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class WebhookApiAnswersTest extends TestCase
{
    /**
     * @param array $responses
     *
     * @return WebhookApi
     */
    protected function fakeApi($responses = [])
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, SerializerFactory::create());

        return new WebhookApi($config);
    }

    public function testListWebhooks()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('webhook-list-response.json')),
        ]);
        $result = $api->listWebhooks();
        self::assertInstanceOf(CollectionInterface::class, $result);
        self::assertInstanceOf(WebhookInterface::class, $result->first());
    }

    public function testCreateWebhook()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('webhook-response.json')),
        ]);
        $result = $api->createWebhook('https://localhost');
        self::assertInstanceOf(WebhookInterface::class, $result);
    }

    public function testUpdateWebhook()
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('webhook-response.json')),
        ]);
        $result = $api->updateWebhook(14, ['target_url' => 'https://new.localhost']);
        self::assertInstanceOf(WebhookInterface::class, $result);
    }

    public function testGroupCreateFrom()
    {
        $wh = $this->createMock(WebhookInterface::class);
        $collection = new WebhookCollection([$wh]);
        $createFrom = (new \ReflectionObject($collection))->getMethod('createFrom');
        $createFrom->setAccessible(true);

        self::assertEquals($collection, $createFrom->invokeArgs($collection, [[$wh]]));
    }

    public function testGroupElementClass()
    {
        self::assertEquals(WebhookResponse::class, WebhookCollection::elementClass());
    }
}
