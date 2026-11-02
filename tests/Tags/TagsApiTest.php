<?php declare(strict_types=1);

namespace Tests\Tags;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Uploadcare\Api;
use Uploadcare\Apis\TagsApi;
use Uploadcare\Configuration;
use Uploadcare\Exception\TagsException;
use Uploadcare\File\File;
use Uploadcare\File\Tags;
use Uploadcare\Interfaces\Api\TagsApiInterface;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Response\TagsResponse;

class TagsApiTest extends TestCase
{
    private const UUID = '21975c81-7f57-4c7a-aef9-acfe28779f78';

    /**
     * @var array<int, array>
     */
    private array $history = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->history = [];
    }

    protected function fileContents(string $name): string
    {
        return \file_get_contents(\sprintf('%s/_data/%s', \dirname(__DIR__), $name));
    }

    /**
     * @param array<int, Response> $responses
     */
    protected function getConfig(array $responses = []): ConfigurationInterface
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        $configuration = Configuration::create('demo-public-key', 'demo-private-key');
        $clientProperty = (new \ReflectionObject($configuration))->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($configuration, new Client(['handler' => $stack]));

        return $configuration;
    }

    protected function response(string $fixture): Response
    {
        return new Response(200, [
            'Content-Type' => \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION),
        ], $this->fileContents($fixture));
    }

    protected function lastRequest(): RequestInterface
    {
        self::assertNotEmpty($this->history, 'No request has been made');

        return $this->history[\count($this->history) - 1]['request'];
    }

    public function testApiExposesTagsApi(): void
    {
        self::assertInstanceOf(TagsApiInterface::class, (new Api($this->getConfig()))->tags());
    }

    public function testGetTags(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-response.json')]));
        $result = $api->getTags(self::UUID);

        self::assertInstanceOf(Tags::class, $result);
        self::assertSame(['cat', 'animal'], $result->toArray());

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('api.uploadcare.com', $request->getUri()->getHost());
        self::assertSame(\sprintf('/files/%s/tags/', self::UUID), $request->getUri()->getPath());
        self::assertSame(
            \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION),
            $request->getHeaderLine('Accept')
        );
    }

    public function testGetTagsOfFileWithoutTags(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-empty-response.json')]));

        self::assertCount(0, $api->getTags(self::UUID));
    }

    public function testGetTagsWithEmptyResponseBody(): void
    {
        $api = new TagsApi($this->getConfig([new Response(200, [], '')]));

        self::assertCount(0, $api->getTags(self::UUID));
    }

    public function testGetTagsAcceptsFileInfoInstance(): void
    {
        $file = (new File())->setUuid(self::UUID);
        $api = new TagsApi($this->getConfig([$this->response('file-tags-response.json')]));
        $api->getTags($file);

        self::assertSame(\sprintf('/files/%s/tags/', self::UUID), $this->lastRequest()->getUri()->getPath());
    }

    public function testReplaceTags(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-replace-response.json')]));
        $result = $api->replaceTags(self::UUID, ['Cat', ' animal ', 'CAT', 'cute']);

        self::assertInstanceOf(TagsResponse::class, $result);
        self::assertSame(['cat', 'animal', 'cute'], $result->getTags()->toArray());
        self::assertSame(['animal', 'cute'], $result->getAdded());
        self::assertSame(['old-tag'], $result->getDeleted());

        $request = $this->lastRequest();
        self::assertSame('PUT', $request->getMethod());
        self::assertSame(\sprintf('/files/%s/tags/', self::UUID), $request->getUri()->getPath());
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('{"tags":["cat","animal","cute"]}', (string) $request->getBody());
    }

    public function testReplaceTagsWithEmptyListClearsTags(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-empty-response.json')]));
        $api->replaceTags(self::UUID, []);

        self::assertSame('{"tags":[]}', (string) $this->lastRequest()->getBody());
    }

    public function testUpdateTags(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-update-response.json')]));
        $result = $api->updateTags(self::UUID, ['cat'], ['dog']);

        self::assertInstanceOf(TagsResponse::class, $result);
        self::assertSame(['pet', 'cat'], $result->getTags()->toArray());
        self::assertSame(['cat'], $result->getAdded());
        self::assertSame(['dog'], $result->getDeleted());

        $request = $this->lastRequest();
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('{"add":["cat"],"delete":["dog"]}', (string) $request->getBody());
    }

    public function testUpdateTagsWithAddOnly(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-update-response.json')]));
        $api->updateTags(self::UUID, ['dog', 'outdoor']);

        self::assertSame('{"add":["dog","outdoor"]}', (string) $this->lastRequest()->getBody());
    }

    public function testUpdateTagsWithDeleteOnly(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-update-response.json')]));
        $api->updateTags(self::UUID, [], ['cat']);

        self::assertSame('{"delete":["cat"]}', (string) $this->lastRequest()->getBody());
    }

    /**
     * An empty array would be encoded as `[]`, while the endpoint expects an object.
     */
    public function testUpdateTagsWithoutArgumentsSendsEmptyObject(): void
    {
        $api = new TagsApi($this->getConfig([$this->response('file-tags-response.json')]));
        $api->updateTags(self::UUID);

        self::assertSame('{}', (string) $this->lastRequest()->getBody());
    }

    public function provideInvalidTags(): array
    {
        return [
            'space' => ['not a tag'],
            'plus' => ['plus+sign'],
            'at' => ['at@sign'],
            'unicode' => ['котик'],
            'too long' => [\str_repeat('a', Tags::MAX_TAG_LENGTH + 1)],
        ];
    }

    /**
     * @dataProvider provideInvalidTags
     */
    public function testReplaceTagsValidatesBeforeRequest(string $tag): void
    {
        $api = new TagsApi($this->getConfig());

        try {
            $api->replaceTags(self::UUID, [$tag]);
            self::fail(\sprintf('TagsException was not thrown for \'%s\'', $tag));
        } catch (TagsException $e) {
            self::assertEmpty($this->history, 'Request must not be made with invalid tags');
        }
    }

    public function testUpdateTagsValidatesAddBeforeRequest(): void
    {
        $api = new TagsApi($this->getConfig());

        $this->expectException(TagsException::class);
        try {
            $api->updateTags(self::UUID, ['not a tag']);
        } finally {
            self::assertEmpty($this->history, 'Request must not be made with invalid tags');
        }
    }

    public function testUpdateTagsValidatesDeleteBeforeRequest(): void
    {
        $api = new TagsApi($this->getConfig());

        $this->expectException(TagsException::class);
        try {
            $api->updateTags(self::UUID, [], ['not a tag']);
        } finally {
            self::assertEmpty($this->history, 'Request must not be made with invalid tags');
        }
    }

    public function testTooManyTagsRejectedBeforeRequest(): void
    {
        $tags = [];
        for ($i = 0; $i <= Tags::MAX_TAGS; ++$i) {
            $tags[] = \sprintf('tag-%d', $i);
        }

        $api = new TagsApi($this->getConfig());

        $this->expectException(TagsException::class);
        try {
            $api->replaceTags(self::UUID, $tags);
        } finally {
            self::assertEmpty($this->history, 'Request must not be made with invalid tags');
        }
    }
}
