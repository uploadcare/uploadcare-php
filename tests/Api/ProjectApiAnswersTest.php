<?php declare(strict_types=1);

namespace Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\ProjectApi;
use Uploadcare\Configuration;
use Uploadcare\Response\ProjectInfoResponse;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

class ProjectApiAnswersTest extends TestCase
{
    protected function fakeApi(array $responses = []): ProjectApi
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);
        $config = new Configuration('public-key', new Signature('private-key'), $client, SerializerFactory::create());

        return new ProjectApi($config);
    }

    public function testProjectInfo(): void
    {
        $api = $this->fakeApi([
            new Response(200, [], DataFile::contents('project-info-response.json')),
        ]);

        $result = $api->getProjectInfo();
        self::assertInstanceOf(ProjectInfoResponse::class, $result);
        self::assertTrue($result->isAutostoreEnabled());
        self::assertNotEmpty($result->getName());
        self::assertNotEmpty($result->getPubKey());
    }
}
