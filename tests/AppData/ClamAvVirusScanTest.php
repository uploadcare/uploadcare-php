<?php declare(strict_types=1);

namespace Tests\AppData;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\AppData\ClamAvData;
use Uploadcare\File\AppData\ClamAvVirusScan;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class ClamAvVirusScanTest extends TestCase
{
    private string $clam;
    private array $dataArray;

    protected function setUp(): void
    {
        parent::setUp();
        $fileInfo = \file_get_contents(\dirname(__DIR__) . '/_data/file-info.json');
        $fileInfoArray = \json_decode($fileInfo, true, 512, JSON_THROW_ON_ERROR);
        $clam = $fileInfoArray['appdata']['uc_clamav_virus_scan'] ?? null;

        self::assertIsArray($clam);
        $this->clam = \json_encode($clam, JSON_THROW_ON_ERROR);
        $this->dataArray = $clam;
    }

    protected function getSerializer(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }

    public function testDeserialization(): void
    {
        $result = $this->getSerializer()->deserialize($this->clam, ClamAvVirusScan::class);
        self::assertInstanceOf(ClamAvVirusScan::class, $result);
        self::assertInstanceOf(\DateTimeInterface::class, $result->getDatetimeCreated());
        self::assertInstanceOf(\DateTimeInterface::class, $result->getDatetimeUpdated());
        self::assertSame('2022-09-18', $result->getDatetimeCreated()->format('Y-m-d'));
        self::assertSame('0.105.1', $result->getVersion());

        $data = $result->getData();
        self::assertInstanceOf(ClamAvData::class, $data);
        self::assertFalse($data->isInfected());
    }

    public function testInfected(): void
    {
        $infected = $this->dataArray;
        $infected['data']['infected'] = true;
        $infected['data']['infected_with'] = 'COVID';

        $result = $this->getSerializer()->deserialize(\json_encode($infected, JSON_THROW_ON_ERROR), ClamAvVirusScan::class);
        $data = $result->getData();
        self::assertInstanceOf(ClamAvData::class, $data);
        self::assertTrue($data->isInfected());
        self::assertSame('COVID', $data->getInfectedWith());
    }
}
