<?php declare(strict_types=1);

namespace Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\DataFile;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Configuration;
use Uploadcare\File\File;
use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Response\GroupListResponse;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class GroupApiAnswersTest extends TestCase
{
    protected function getConfig(array $responses): ConfigurationInterface
    {
        $handler = new MockHandler($responses);
        $stack = HandlerStack::create($handler);
        $client = new Client(['handler' => $stack]);

        return new Configuration(
            'public-key',
            new Signature('private-key'),
            $client,
            new Serializer(new SnackCaseConverter())
        );
    }

    public function testCreateGroupWithArray(): void
    {
        $conf = $this->getConfig([new Response(200, [], DataFile::contents('group/create-group-response.json'))]);
        $api = new GroupApi($conf);
        $result = $api->createGroup([\uuid_create()]);

        self::assertEquals(1, $result->getFilesCount());
    }

    public function testCreateGroupWithCollection(): void
    {
        $conf = $this->getConfig([new Response(200, [], DataFile::contents('group/create-group-response.json'))]);
        $api = new GroupApi($conf);
        $collection = new FileCollection([(new File())->setUuid(\uuid_create())]);
        $result = $api->createGroup($collection);

        self::assertEquals(1, $result->getFilesCount());
    }

    public function testGroupInfoResponse(): void
    {
        $conf = $this->getConfig([new Response(200, [], DataFile::contents('group/group-info-response.json'))]);
        $api = new GroupApi($conf);
        $result = $api->groupInfo(\uuid_create());

        self::assertNotEmpty($result->getId());
        self::assertNotEmpty($result->getFilesCount());
        self::assertInstanceOf(\Uploadcare\File::class, $result->getFiles()->first());
    }

    public function testListGroups(): void
    {
        $data = DataFile::contents('group/list-groups-response.json');
        $answers = [
            new Response(200, [], $data),
            new Response(200, [], $data),
        ];
        $conf = $this->getConfig($answers);
        $api = new GroupApi($conf);

        $result = $api->listGroups();
        self::assertInstanceOf(GroupListResponse::class, $result);

        $next = $api->nextPage($result);
        self::assertInstanceOf(GroupListResponse::class, $next);
    }

    public function testNextPageWithNull(): void
    {
        $data = (new GroupListResponse())->setNext(null);
        $conf = $this->getConfig([]);
        $api = new GroupApi($conf);

        self::assertNull($api->nextPage($data));
    }
}
