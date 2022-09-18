<?php declare(strict_types=1);

namespace Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Uploadcare\Interfaces\File\ContentInfoInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Response\FileListResponse;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class DeserializeCollectionTest extends TestCase
{
    /**
     * @var string
     */
    private $path;

    protected function setUp(): void
    {
        $this->path = \dirname(__DIR__) . '/_data/file-list-api-response.json';
    }

    public function testDeserializeCollection(): void
    {
        $content = \file_get_contents($this->path);
        $serializer = new Serializer(new SnackCaseConverter());

        /** @var ListResponseInterface $result */
        $result = $serializer->deserialize($content, FileListResponse::class);
        self::assertInstanceOf(ListResponseInterface::class, $result);
        self::assertInstanceOf(FileInfoInterface::class, $result->getResults()->first());

        /** @var FileInfoInterface $file */
        $file = $result->getResults()->first();

        self::assertNull($file->getDatetimeRemoved());
        self::assertInstanceOf(\DateTimeInterface::class, $file->getDatetimeStored());
        self::assertInstanceOf(\DateTimeInterface::class, $file->getDatetimeUploaded());
        self::assertInstanceOf(ContentInfoInterface::class, $file->getContentInfo());
        self::assertTrue($file->isImage());
        self::assertTrue($file->isReady());
        self::assertIsString($file->getMimeType());
        self::assertNotEmpty($file->getOriginalFileUrl());
        self::assertNotEmpty($file->getOriginalFilename());
    }
}
