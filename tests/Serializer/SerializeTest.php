<?php

namespace Tests\Serializer;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Uploadcare\File\File;
use Uploadcare\File\GeoLocation;
use Uploadcare\File\ImageInfo;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class SerializeTest extends TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    protected function setUp()
    {
        $this->faker = Factory::create();
    }

    /**
     * @return File
     */
    protected function makeFile()
    {
        $geo = (new GeoLocation())
            ->setLatitude($this->faker->latitude)
            ->setLongitude($this->faker->longitude)
        ;

        $imageInfo = (new ImageInfo())
            ->setColorMode('CMYK')
            ->setOrientation(0)
            ->setFormat('TIFF')
            ->setIsSequence(false)
            ->setHeight(10240)
            ->setWidth(7680)
            ->setGeoLocation($geo)
            ->setDatetimeOriginal(\date_create())
            ->setDpi([300, 300])
        ;

        return (new File())
            ->setDatetimeStored(\date_create())
            ->setDatetimeUploaded(\date_create())
            ->setImageInfo($imageInfo)
            ->setIsImage(true)
            ->setIsReady(true)
            ->setMimeType('image/tiff')
            ->setOriginalFileUrl('https://example.com/file.tiff')
            ->setOriginalFilename('original-name.tiff')
            ->setSize(1023548)
            ->setUrl('https://api.uploadcare.com/files/3c269810-c17b-4e2c-92b6-25622464d866/')
            ->setUuid(\uuid_create())
        ;
    }

    public function testNormalize()
    {
        $file = $this->makeFile();

        $serializer = new Serializer(new SnackCaseConverter());
        $normalize = (new \ReflectionObject($serializer))->getMethod('normalize');
        $normalize->setAccessible(true);

        $result = [];
        $normalize->invokeArgs($serializer, [$file, &$result]);

        $this->assertArrayHasKey('datetime_removed', $result);
        $this->assertNull($result['datetime_removed']);
        $this->assertEquals($result['datetime_stored'], $file->getDatetimeStored()->format(Serializer::DATE_FORMAT));
        $this->assertEquals($result['image_info']['color_mode'], $file->getImageInfo()->getColorMode());
        $this->assertEquals($result['is_image'], $file->isImage());
    }

    public function testSerializeMethod()
    {
        $file = $this->makeFile();
        $serializer = new Serializer(new SnackCaseConverter());
        $result = $serializer->serialize($file);

        $this->assertContains('datetime_removed', $result);
        $this->assertContains('image_info', $result);
        $this->assertContains('color_mode', $result);
    }
}
