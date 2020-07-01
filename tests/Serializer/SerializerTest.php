<?php

namespace Tests\Serializer;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Uploadcare\File\ImageInfo;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class SerializerTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    protected function setUp()
    {
        $this->faker = Factory::create();
    }

    public function testDenormalizeImageInfo()
    {
        $serializer = new Serializer(new SnackCaseConverter());

        $lon = $this->faker->longitude;
        $lat = $this->faker->latitude;
        $date = \date_create();

        $data = \json_decode(\file_get_contents(\dirname(__DIR__) . '/_data/file-example.json'), true);
        $imageInfo = $data['image_info'];
        $imageInfo['geo_location'] = [
            'latitude' => $lat,
            'longitude' => $lon,
        ];
        $imageInfo['datetime_original'] = $date->format('Y-m-d\TH:i:s.u\Z');

        /** @var ImageInfo $object */
        $object = $serializer->deserialize(\json_encode($imageInfo), ImageInfo::class);

        $this->assertInstanceOf(ImageInfo::class, $object);

        $this->assertEquals('RGB', $object->getColorMode());
        $this->assertNull($object->getOrientation());
        $this->assertEquals('JPEG', $object->getFormat());
        $this->assertFalse($object->isSequence());
        $this->assertEquals(500, $object->getHeight());
        $this->assertEquals(800, $object->getWidth());
        $this->assertEquals($lat, $object->getGeoLocation()->getLatitude());
        $this->assertEquals($lon, $object->getGeoLocation()->getLongitude());
        $this->assertInstanceOf(\DateTimeInterface::class, $object->getDatetimeOriginal());
        $this->assertEquals([144, 144], $object->getDpi());
    }
}
