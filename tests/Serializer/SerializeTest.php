<?php declare(strict_types=1);

namespace Tests\Serializer;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Uploadcare\File\ContentInfo\Audio;
use Uploadcare\File\ContentInfo\ContentInfo;
use Uploadcare\File\ContentInfo\GeoLocation;
use Uploadcare\File\ContentInfo\ImageInfo;
use Uploadcare\File\ContentInfo\Video;
use Uploadcare\File\ContentInfo\VideoInfo;
use Uploadcare\File\File;
use Uploadcare\Interfaces\Serializer\SerializerInterface;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;

class SerializeTest extends TestCase
{
    private Generator $faker;

    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->serializer = new Serializer(new SnackCaseConverter());
    }

    protected function makeFile(): File
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
            ->setIsImage(true)
            ->setContentInfo((new ContentInfo())->setImage($imageInfo))
            ->setIsReady(true)
            ->setMimeType('image/tiff')
            ->setOriginalFileUrl('https://example.com/file.tiff')
            ->setOriginalFilename('original-name.tiff')
            ->setSize(1023548)
            ->setUrl('https://api.uploadcare.com/files/3c269810-c17b-4e2c-92b6-25622464d866/')
            ->setUuid(\uuid_create())
        ;
    }

    public function testNormalize(): void
    {
        $file = $this->makeFile();

        $normalize = (new \ReflectionObject($this->serializer))->getMethod('normalize');
        $normalize->setAccessible(true);

        $result = [];
        $normalize->invokeArgs($this->serializer, [$file, &$result]);

        self::assertArrayHasKey('datetime_removed', $result);
        self::assertNull($result['datetime_removed']);
        self::assertEquals($result['datetime_stored'], $file->getDatetimeStored()->format(Serializer::DATE_FORMAT));
        self::assertEquals($result['content_info']['image']['color_mode'], $file->getContentInfo()->getImage()->getColorMode());
        self::assertEquals($result['is_image'], $file->isImage());
    }

    public function testSerializeMethod(): void
    {
        $file = $this->makeFile();
        $result = $this->serializer->serialize($file);

        self::assertStringContainsString('datetime_removed', $result);
        self::assertStringContainsString('content_info', $result);
        self::assertStringContainsString('color_mode', $result);
    }

    public function testVariationsArray(): void
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

    public function testVideoInfo(): void
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
        $file->setContentInfo((new ContentInfo())->setVideo($videoInfo));

        $normalize = (new \ReflectionObject($this->serializer))->getMethod('normalize');
        $normalize->setAccessible(true);

        $result = [];
        $normalize->invokeArgs($this->serializer, [$file, &$result]);

        self::assertSame('avi', $result['content_info']['video']['format']);
        self::assertSame('DivX', $result['content_info']['video']['video']['codec']);
        self::assertSame(1024, $result['content_info']['video']['video']['width']);
        self::assertSame('mp3', $result['content_info']['video']['audio']['codec']);
        self::assertSame('5.1', $result['content_info']['video']['audio']['channels']);
        self::assertSame(222, $result['content_info']['video']['audio']['sample_rate']);
    }
}
