<?php declare(strict_types=1);

namespace Tests\Tags;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Uploadcare\Apis\FileApi;
use Uploadcare\Configuration;
use Uploadcare\File\File;
use Uploadcare\File\Tags;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Serializer\SerializerFactory;

class FileTagsDeserializationTest extends TestCase
{
    protected function fileContents(string $name): string
    {
        return \file_get_contents(\sprintf('%s/_data/%s', \dirname(__DIR__), $name));
    }

    protected function deserializeFile(string $json): File
    {
        $result = SerializerFactory::create()->deserialize($json, File::class);
        self::assertInstanceOf(File::class, $result);

        return $result;
    }

    protected function getConfig(array $responses): ConfigurationInterface
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler($responses))]);

        $configuration = Configuration::create('demo-public-key', 'demo-private-key');
        $clientProperty = (new \ReflectionObject($configuration))->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($configuration, $client);

        return $configuration;
    }

    public function testTagsFromFileInfo(): void
    {
        $file = $this->deserializeFile($this->fileContents('file-info-api-response.json'));

        self::assertInstanceOf(Tags::class, $file->getTags());
        self::assertSame(['cat', 'animal'], $file->getTags()->toArray());
        self::assertCount(2, $file->getTags());
        self::assertTrue($file->getTags()->has('cat'));
        self::assertFalse($file->getTags()->has('dog'));
    }

    public function testTagsAreEmptyWhenKeyIsAbsent(): void
    {
        $file = $this->deserializeFile($this->fileContents('file-info.json'));

        self::assertInstanceOf(Tags::class, $file->getTags());
        self::assertCount(0, $file->getTags());
        self::assertSame([], $file->getTags()->toArray());
    }

    public function testTagsAreEmptyWhenListIsEmpty(): void
    {
        $file = $this->deserializeFile('{"uuid": "some-uuid", "tags": []}');

        self::assertCount(0, $file->getTags());
    }

    public function testNewFileHasEmptyTags(): void
    {
        self::assertCount(0, (new File())->getTags());
    }

    public function testSetTags(): void
    {
        $file = (new File())->setTags(['Cat', 'cat', 'ANIMAL']);

        self::assertInstanceOf(FileInfoInterface::class, $file);
        self::assertSame(['cat', 'animal'], $file->getTags()->toArray());
    }

    public function testTagsInFileList(): void
    {
        $api = new FileApi($this->getConfig([
            new Response(200, [], $this->fileContents('file-list-api-response.json')),
        ]));

        $result = $api->listFiles();
        self::assertInstanceOf(ListResponseInterface::class, $result);

        $files = [];
        foreach ($result->getResults() as $file) {
            $files[] = $file;
        }

        self::assertSame(['cat', 'animal'], $files[0]->getTags()->toArray(), 'Tags of the first file');
        self::assertCount(0, $files[1]->getTags(), 'An empty tag list');
        self::assertCount(0, $files[2]->getTags(), 'No tags key in the payload');
    }

    public function testTagsThroughFileInfoCall(): void
    {
        $api = new FileApi($this->getConfig([
            new Response(200, [], $this->fileContents('file-info-api-response.json')),
        ]));

        $file = $api->fileInfo('a1cfbdb0-8293-4f72-b5ec-4f93c40303dc');
        self::assertInstanceOf(\Uploadcare\File::class, $file);
        self::assertSame(['cat', 'animal'], $file->getTags()->toArray());
    }

    /**
     * Reading tags must not call the API.
     */
    public function testDecoratorDoesNotCallApi(): void
    {
        $inner = $this->deserializeFile($this->fileContents('file-info-api-response.json'));
        $api = $this->createMock(FileApiInterface::class);
        $api->expects(self::never())->method('fileInfo');
        $api->expects(self::never())->method('getMetadata');

        $decorated = new \Uploadcare\File($inner, $api);

        self::assertSame(['cat', 'animal'], $decorated->getTags()->toArray());
    }
}
