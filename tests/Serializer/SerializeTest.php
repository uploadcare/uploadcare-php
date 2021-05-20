<?php

namespace Tests\Serializer;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Uploadcare\File\Audio;
use Uploadcare\File\File;
use Uploadcare\File\GeoLocation;
use Uploadcare\File\ImageInfo;
use Uploadcare\File\Video;
use Uploadcare\File\VideoInfo;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class SerializeTest extends TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    private $serializer;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->serializer = new Serializer(new SnackCaseConverter());
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

        $normalize = (new \ReflectionObject($this->serializer))->getMethod('normalize');
        $normalize->setAccessible(true);

        $result = [];
        $normalize->invokeArgs($this->serializer, [$file, &$result]);

        self::assertArrayHasKey('datetime_removed', $result);
        self::assertNull($result['datetime_removed']);
        self::assertEquals($result['datetime_stored'], $file->getDatetimeStored()->format(Serializer::DATE_FORMAT));
        self::assertEquals($result['image_info']['color_mode'], $file->getImageInfo()->getColorMode());
        self::assertEquals($result['is_image'], $file->isImage());
    }

    public function testSerializeMethod()
    {
        $file = $this->makeFile();
        $result = $this->serializer->serialize($file);

        self::assertStringContainsString('datetime_removed', $result);
        self::assertStringContainsString('image_info', $result);
        self::assertStringContainsString('color_mode', $result);
    }

    public function testVariationsArray()
    {
        $file = $this->makeFile();
        $variations = [
            'video/mp4' => \uuid_create(),
            'video/mov' => \uuid_create(),
        ];
        $file->setVariations($variations);

        $normalize = (new \ReflectionObject($this->serializer))->getMethod('normalize');
        $normalize->setAccessible(true);

        $result = [];
        $normalize->invokeArgs($this->serializer, [$file, &$result]);

        self::assertArrayHasKey('variations', $result);
        self::assertSame($variations, $result['variations']);
    }

    public function testVideoInfo()
    {
        $file = $this->makeFile();
        $video = (new Video())
            ->setWidth(1024)
            ->setHeight(768)
            ->setBitrate(24)
            ->setCodec('DivX')
            ->setFrameRate(9.68)
        ;
        $audio = (new Audio())
            ->setCodec('mp3')
            ->setBitrate(360)
            ->setChannels('5.1')
            ->setSampleRate(222)
        ;
        $videoInfo = (new VideoInfo())
            ->setBitrate(24)
            ->setFormat('avi')
            ->setDuration(135)
            ->setVideo($video)
            ->setAudio($audio)
        ;
        $file->setVideoInfo($videoInfo);

        $normalize = (new \ReflectionObject($this->serializer))->getMethod('normalize');
        $normalize->setAccessible(true);

        $result = [];
        $normalize->invokeArgs($this->serializer, [$file, &$result]);

        self::assertSame('avi', $result['video_info']['format']);
        self::assertSame('DivX', $result['video_info']['video']['codec']);
        self::assertSame(1024, $result['video_info']['video']['width']);
        self::assertSame('mp3', $result['video_info']['audio']['codec']);
        self::assertSame('5.1', $result['video_info']['audio']['channels']);
        self::assertSame(222, $result['video_info']['audio']['sample_rate']);
    }
}
