<?php declare(strict_types=1);

namespace Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Uploadcare\Apis\AddonsApi;
use Uploadcare\Configuration;
use Uploadcare\Conversion\RemoveBackgroundRequest;
use Uploadcare\Interfaces\Api\AddonsApiInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class AddonsApiTest extends TestCase
{
    protected function fakeApi(array $responses = []): AddonsApiInterface
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public', new Signature('private'), $client, SerializerFactory::create());

        return new AddonsApi($config);
    }

    public function testRecognitionMethods(): void
    {
        $requestId = \uuid_create();

        $api = $this->fakeApi([
            new Response(200, [], \json_encode(['request_id' => $requestId])),
            new Response(200, [], \json_encode(['status' => 'done'])),
        ]);

        $result = $api->requestAwsRecognition(\uuid_create());
        self::assertSame($result, $requestId);

        $status = $api->checkAwsRecognition($requestId);
        self::assertSame('done', $status);
    }

    public function testVirusScanMethods(): void
    {
        $requestId = \uuid_create();
        $api = $this->fakeApi([
            new Response(200, [], \json_encode(['request_id' => $requestId])),
            new Response(200, [], \json_encode(['status' => 'done'])),
        ]);
        self::assertSame($requestId, $api->requestAntivirusScan(\uuid_create()));
        self::assertSame('done', $api->checkAntivirusScan($requestId));
    }

    public function testRemoveBackgroundMethods(): void
    {
        $requestId = \uuid_create();
        $api = $this->fakeApi([
            new Response(200, [], \json_encode(['request_id' => $requestId])),
            new Response(200, [], \json_encode(['status' => 'done'])),
        ]);
        $rr = (new RemoveBackgroundRequest())->setType('person');

        self::assertSame($requestId, $api->requestRemoveBackground(\uuid_create(), $rr));
        self::assertSame('done', $api->checkRemoveBackground($requestId));
    }

    public function testRecognitionModerationMethods(): void
    {
        $id = \uuid_create();

        $api = $this->fakeApi([
            new Response(200, [], \json_encode(['request_id' => $id])),
            new Response(200, [], \json_encode(['status' => 'done'])),
        ]);

        $result = $api->requestAwsRecognitionModeration(\uuid_create());
        self::assertSame($result, $id);

        $status = $api->checkAwsRecognitionModeration($id);
        self::assertSame('done', $status);
    }
}
