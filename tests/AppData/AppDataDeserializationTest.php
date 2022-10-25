<?php declare(strict_types=1);

namespace Tests\AppData;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\AppData\AwsRecognitionLabels;
use Uploadcare\File\AppData\ClamAvVirusScan;
use Uploadcare\File\AppData\RemoveBg;
use Uploadcare\File\File;
use Uploadcare\Interfaces\File\AppDataInterface;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class AppDataDeserializationTest extends TestCase
{
    private string $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json');
    }

    protected function getSerializer(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }

    public function testDeserialization(): void
    {
        $result = $this->getSerializer()->deserialize($this->data, File::class);
        $appData = $result->getAppdata();
        self::assertInstanceOf(AppDataInterface::class, $appData);

        self::assertInstanceOf(AwsRecognitionLabels::class, $appData->getAwsRekognitionDetectLabels());
        self::assertInstanceOf(ClamAvVirusScan::class, $appData->getClamAvVirusScan());
        self::assertInstanceOf(RemoveBg::class, $appData->getRemoveBg());
    }
}
