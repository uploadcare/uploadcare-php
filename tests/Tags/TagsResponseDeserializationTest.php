<?php declare(strict_types=1);

namespace Tests\Tags;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\Tags;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Response\TagsResponse;
use Uploadcare\Serializer\SerializerFactory;

class TagsResponseDeserializationTest extends TestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return SerializerFactory::create();
    }

    protected function deserialize(string $path): TagsResponse
    {
        $contents = \file_get_contents(\sprintf('%s/_data/%s', \dirname(__DIR__), $path));
        $result = $this->getSerializer()->deserialize($contents, TagsResponse::class);
        self::assertInstanceOf(TagsResponse::class, $result);

        return $result;
    }

    public function testTagsOnlyResponse(): void
    {
        $result = $this->deserialize('file-tags-response.json');

        self::assertInstanceOf(Tags::class, $result->getTags());
        self::assertSame(['cat', 'animal'], $result->getTags()->toArray());
        self::assertSame([], $result->getAdded());
        self::assertSame([], $result->getDeleted());
    }

    public function testEmptyTagsResponse(): void
    {
        $result = $this->deserialize('file-tags-empty-response.json');

        self::assertCount(0, $result->getTags());
        self::assertSame([], $result->getTags()->toArray());
        self::assertSame([], $result->getAdded());
        self::assertSame([], $result->getDeleted());
    }

    public function testReplaceResponse(): void
    {
        $result = $this->deserialize('file-tags-replace-response.json');

        self::assertSame(['cat', 'animal', 'cute'], $result->getTags()->toArray());
        self::assertSame(['animal', 'cute'], $result->getAdded());
        self::assertSame(['old-tag'], $result->getDeleted());
    }

    public function testUpdateResponse(): void
    {
        $result = $this->deserialize('file-tags-update-response.json');

        self::assertSame(['pet', 'cat'], $result->getTags()->toArray());
        self::assertSame(['cat'], $result->getAdded());
        self::assertSame(['dog'], $result->getDeleted());
    }

    public function testEmptyPayloadKeepsDefaults(): void
    {
        $result = $this->getSerializer()->deserialize('{}', TagsResponse::class);
        self::assertInstanceOf(TagsResponse::class, $result);

        self::assertCount(0, $result->getTags());
        self::assertSame([], $result->getAdded());
        self::assertSame([], $result->getDeleted());
    }
}
