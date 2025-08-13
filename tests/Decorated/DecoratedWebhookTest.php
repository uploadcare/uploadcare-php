<?php declare(strict_types=1);

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
    protected function fakeApi(array $responses = []): WebhookApiInterface
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, SerializerFactory::create());

        return new WebhookApi($config);
    }

    public function commonMethods(): array
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
     * @throws \ReflectionException
     */
    public function testCommonMethods(string $method): void
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

    public function testCreateFromMethod(): void
    {
        $wh = SerializerFactory::create()->deserialize(DataFile::contents('webhook-response.json'), WebhookResponse::class);
        $collection = new WebhookCollection(new \Uploadcare\Response\WebhookCollection([$wh]), $this->fakeApi());
        $createFrom = (new \ReflectionObject($collection))->getMethod('createFrom');
        $createFrom->setAccessible(true);

        $result = $createFrom->invokeArgs($collection, [[$wh]]);
        self::assertEquals($collection, $result);
    }

    public function testElementClass(): void
    {
        self::assertEquals(Webhook::class, WebhookCollection::elementClass());
    }

    public function testDeleteMethod(): void
    {
        $api = $this->fakeApi([
            new Response(204),
        ]);
        $wh = $this->createMock(WebhookInterface::class);
        $wh->method('getTargetUrl')->willReturn('https://localhost.com/wh');

        $decorated = new Webhook($wh, $api);
        self::assertTrue($decorated->delete());
    }

    public function provideDecoratedMethods(): array
    {
        return [
            ['activate'],
            ['deactivate'],
            ['updateUrl'],
        ];
    }

    /**
     * @dataProvider provideDecoratedMethods
     */
    public function testDecoratedMethods(string $method): void
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('webhook-response.json')),
        ]);
        $wh = $this->createMock(WebhookInterface::class);
        $wh->method('getId')->willReturn(\random_int(0, 10000));

        $decorated = new Webhook($wh, $api);
        $result = $decorated->{$method}('https://new-url.com');
        self::assertInstanceOf(Webhook::class, $result);
    }

    public function testMainApiMethod(): void
    {
        $api = new Api(Configuration::create('public', 'private'));
        self::assertInstanceOf(WebhookApiInterface::class, $api->webhook());
    }
}
