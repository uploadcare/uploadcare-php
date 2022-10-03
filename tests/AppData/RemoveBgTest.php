<?php declare(strict_types=1);

namespace Tests\AppData;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\AppData\RemoveBg;
use Uploadcare\File\AppData\RemoveBgData;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class RemoveBgTest extends TestCase
{
    private string $removeBg;

    protected function setUp(): void
    {
        parent::setUp();
        $fileInfo = \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json');
        $fileInfoArray = \json_decode($fileInfo, true, 512, JSON_THROW_ON_ERROR);
        $removeBg = $fileInfoArray['appdata']['remove_bg'] ?? null;
        self::assertIsArray($removeBg);

        $this->removeBg = \json_encode($removeBg, JSON_THROW_ON_ERROR);
    }

    protected function getSerializer(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }

    public function testDeserialization(): void
    {
        $result = $this->getSerializer()->deserialize($this->removeBg, RemoveBg::class);
        self::assertInstanceOf(RemoveBg::class, $result);
        self::assertSame('2022-10-03', $result->getDatetimeCreated()->format('Y-m-d'));
        self::assertSame('2022-10-03', $result->getDatetimeUpdated()->format('Y-m-d'));
        self::assertSame('1.0', $result->getVersion());

        $data = $result->getData();
        self::assertInstanceOf(RemoveBgData::class, $data);
        self::assertSame('product', $data->getForegroundType());
    }
}
