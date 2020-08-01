<?php


namespace Tests\Decorated;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\WebhookApi;
use Uploadcare\Configuration;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

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
     * @param $method
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
}
