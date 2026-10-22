<?php declare(strict_types=1);

namespace Tests\Tags;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Configuration;
use Uploadcare\Exception\TagsException;
use Uploadcare\File\Tags;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;
use Uploadcare\Uploader\Uploader;

class UploaderTagsTest extends TestCase
{
    /**
     * Raw bodies of the requests made by the uploader, in order.
     *
     * @var array<int, array{body: string, boundary: string, uri: string}>
     */
    private array $requests = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->requests = [];
    }

    protected function makeConfiguration(array $responses): ConfigurationInterface
    {
        $handler = new MockHandler($responses);
        $client = new Client(['handler' => HandlerStack::create($handler)]);

        return new Configuration('demo-public-key', new Signature('demo-private-key'), $client, SerializerFactory::create());
    }

    /**
     * The uploader closes the file handle after sending, so the body must be read at send time.
     */
    protected function capture(array $body): callable
    {
        return function (RequestInterface $request) use ($body): ResponseInterface {
            \preg_match('/boundary=([^\s;]+)/', $request->getHeaderLine('Content-Type'), $matches);
            $this->requests[] = [
                'body' => (string) $request->getBody(),
                'boundary' => $matches[1] ?? '',
                'uri' => (string) $request->getUri(),
            ];

            return new Response(200, [], \json_encode($body));
        };
    }

    protected function fileInfoResponse(): Response
    {
        return new Response(200, [], \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json'));
    }

    /**
     * Value of a single `multipart/form-data` field, or null when the field is absent.
     */
    protected function field(int $requestIndex, string $name): ?string
    {
        self::assertArrayHasKey($requestIndex, $this->requests, 'No such request');
        $request = $this->requests[$requestIndex];

        foreach (\explode('--' . $request['boundary'], $request['body']) as $part) {
            if (\strpos($part, \sprintf('name="%s"', $name)) === false) {
                continue;
            }
            $chunks = \explode("\r\n\r\n", $part, 2);

            return isset($chunks[1]) ? \rtrim($chunks[1], "\r\n") : null;
        }

        return null;
    }

    protected function fieldCount(int $requestIndex, string $name): int
    {
        return \substr_count($this->requests[$requestIndex]['body'], \sprintf('name="%s"', $name));
    }

    public function testDirectUploadSendsTags(): void
    {
        $configuration = $this->makeConfiguration([
            $this->capture(['file' => '21975c81-7f57-4c7a-aef9-acfe28779f78']),
            $this->fileInfoResponse(),
        ]);

        (new Uploader($configuration))->fromContent('content', 'text/plain', 'file.txt', 'auto', [], ['Cat', ' animal ', 'CAT']);

        self::assertStringContainsString('/base/', $this->requests[0]['uri']);
        self::assertSame(1, $this->fieldCount(0, 'tags'), 'Tags must be sent as a single field');
        self::assertSame('cat,animal', $this->field(0, 'tags'));
    }

    public function testDirectUploadWithoutTagsSendsNoField(): void
    {
        $configuration = $this->makeConfiguration([
            $this->capture(['file' => '21975c81-7f57-4c7a-aef9-acfe28779f78']),
            $this->fileInfoResponse(),
        ]);

        (new Uploader($configuration))->fromContent('content', 'text/plain', 'file.txt');

        self::assertSame(0, $this->fieldCount(0, 'tags'));
        self::assertNull($this->field(0, 'tags'));
    }

    public function testTagsDoNotReplaceMetadata(): void
    {
        $configuration = $this->makeConfiguration([
            $this->capture(['file' => '21975c81-7f57-4c7a-aef9-acfe28779f78']),
            $this->fileInfoResponse(),
        ]);

        (new Uploader($configuration))
            ->fromContent('content', 'text/plain', 'file.txt', 'auto', ['pet' => 'cat'], ['animal']);

        self::assertSame('cat', $this->field(0, 'metadata[pet]'));
        self::assertSame('animal', $this->field(0, 'tags'));
    }

    public function testFromUrlSendsTags(): void
    {
        $configuration = $this->makeConfiguration([
            $this->capture(['token' => 'some-token']),
        ]);

        (new Uploader($configuration))->fromUrl('https://example.com/file.jpg', null, null, 'auto', [], ['cat']);

        self::assertStringContainsString('/from_url/', $this->requests[0]['uri']);
        self::assertSame('https://example.com/file.jpg', $this->field(0, 'source_url'));
        self::assertSame('cat', $this->field(0, 'tags'));
    }

    public function testFromUrlWithoutTagsSendsNoField(): void
    {
        $configuration = $this->makeConfiguration([
            $this->capture(['token' => 'some-token']),
        ]);

        (new Uploader($configuration))->fromUrl('https://example.com/file.jpg');

        self::assertSame(0, $this->fieldCount(0, 'tags'));
    }

    public function testMultipartUploadSendsTagsOnStartOnly(): void
    {
        // Sparse write: the uploader only looks at the size, so there is no need to
        // materialize 100 Mb of content.
        $handle = \fopen('php://temp', 'rb+');
        \fseek($handle, Uploader::MULTIPART_UPLOAD_SIZE - 1);
        \fwrite($handle, 'a');
        \rewind($handle);

        $configuration = $this->makeConfiguration([
            // multipart/start/
            $this->capture([
                'uuid' => '21975c81-7f57-4c7a-aef9-acfe28779f78',
                'parts' => ['https://example.com/part-1'],
            ]),
            // the part itself
            $this->capture([]),
            // multipart/complete/
            $this->capture(['uuid' => '21975c81-7f57-4c7a-aef9-acfe28779f78']),
            $this->fileInfoResponse(),
        ]);

        (new Uploader($configuration))->fromResource($handle, 'text/plain', 'big.txt', 'auto', [], ['cat', 'animal']);

        self::assertCount(3, $this->requests);
        self::assertStringContainsString('/multipart/start/', $this->requests[0]['uri']);
        self::assertSame('cat,animal', $this->field(0, 'tags'), 'Start request carries the tags');

        self::assertSame(0, $this->fieldCount(1, 'tags'), 'Part request must not carry tags');
        self::assertStringContainsString('/multipart/complete/', $this->requests[2]['uri']);
        self::assertSame(0, $this->fieldCount(2, 'tags'), 'Complete request must not carry tags');
    }

    public function provideInvalidTags(): array
    {
        return [
            'space' => [['not a tag']],
            'plus' => [['plus+sign']],
            'unicode' => [['котик']],
            'too long' => [[\str_repeat('a', Tags::MAX_TAG_LENGTH + 1)]],
        ];
    }

    /**
     * @dataProvider provideInvalidTags
     */
    public function testInvalidTagsRejectedBeforeUpload(array $tags): void
    {
        $configuration = $this->makeConfiguration([
            $this->capture(['file' => '21975c81-7f57-4c7a-aef9-acfe28779f78']),
        ]);

        $this->expectException(TagsException::class);
        try {
            (new Uploader($configuration))->fromContent('content', 'text/plain', 'file.txt', 'auto', [], $tags);
        } finally {
            self::assertEmpty($this->requests, 'Nothing must be sent when tags are invalid');
        }
    }

    public function testTooManyTagsRejectedBeforeUpload(): void
    {
        $tags = [];
        for ($i = 0; $i <= Tags::MAX_TAGS; ++$i) {
            $tags[] = \sprintf('tag-%d', $i);
        }

        $configuration = $this->makeConfiguration([
            $this->capture(['file' => '21975c81-7f57-4c7a-aef9-acfe28779f78']),
        ]);

        $this->expectException(TagsException::class);
        try {
            (new Uploader($configuration))->fromContent('content', 'text/plain', 'file.txt', 'auto', [], $tags);
        } finally {
            self::assertEmpty($this->requests, 'Nothing must be sent when tags are invalid');
        }
    }
}
