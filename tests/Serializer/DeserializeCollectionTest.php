<?php

namespace Tests\Serializer;

use PHPUnit\Framework\TestCase;
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

    protected function setUp()
    {
        $this->path = \dirname(__DIR__) . '/_data/file-list-api-response.json';
    }

    public function testDeserializeCollection()
    {
        $content = \file_get_contents($this->path);
        $serializer = new Serializer(new SnackCaseConverter());

        /** @var ListResponseInterface $result */
        $result = $serializer->deserialize($content, FileListResponse::class);
        self::assertInstanceOf(ListResponseInterface::class, $result);
        self::assertInstanceOf(FileInfoInterface::class, $result->getResults()->first());
    }
}
