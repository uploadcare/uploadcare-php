<?php declare(strict_types=1);

namespace Tests\VideoEncoding;

use PHPUnit\Framework\TestCase;
use Uploadcare\Conversion\VideoEncodingRequest;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;

class VideoEncodingRequestTest extends TestCase
{
    public function testSetHorizontalSize(): void
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setHorizontalSize(640));
        self::assertEquals(640, $request->getHorizontalSize());
        $request->setHorizontalSize(null);
        self::assertNull($request->getHorizontalSize());
    }

    public function testSetVerticalSize(): void
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setVerticalSize(480));
        self::assertEquals(480, $request->getVerticalSize());
        $request->setVerticalSize(null);
        self::assertNull($request->getVerticalSize());
    }

    public function provideWrongSizes(): array
    {
        return [
            [7],
            [473],
            [0],
        ];
    }

    /**
     * @dataProvider provideWrongSizes
     *
     * @param int|string $size
     */
    public function testWrongHorizontalSize($size): void
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setHorizontalSize($size);
        $this->expectExceptionMessageMatches('/Horizontal size must be an int divisible by 4/');
    }

    /**
     * @dataProvider provideWrongSizes
     *
     * @param int|string $size
     */
    public function testWrongVerticalSize($size): void
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setVerticalSize($size);
        $this->expectExceptionMessageMatches('/Vertical size must be an int divisible by 4/');
    }

    public function testSetResizeMode(): void
    {
        $request = new VideoEncodingRequest();
        $modes = (new \ReflectionObject($request))->getProperty('resizes');
        $modes->setAccessible(true);
        $mode = \array_rand(\array_flip($modes->getValue($request)), 1);

        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setResizeMode($mode));
        self::assertEquals($mode, $request->getResizeMode());
        $request->setResizeMode(null);
        self::assertNull($request->getResizeMode());
    }

    public function provideWrongResizeModes(): array
    {
        return [
            ['some-wrong-resize'],
            [1255],
            [0],
        ];
    }

    /**
     * @dataProvider provideWrongResizeModes
     *
     * @param string|int $mode
     */
    public function testWrongResizeModes($mode): void
    {
        $this->expectException(!\is_string($mode) ? \TypeError::class : InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setResizeMode($mode);
        if (\is_string($mode)) {
            $this->expectExceptionMessageMatches('/is invalid. Use one of/');
        }
    }

    public function testSetQuality(): void
    {
        $request = new VideoEncodingRequest();
        $qualities = (new \ReflectionObject($request))->getProperty('qualities');
        $qualities->setAccessible(true);
        $quality = \array_rand(\array_flip($qualities->getValue($request)), 1);

        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setQuality($quality));
        self::assertEquals($quality, $request->getQuality());
        $request->setQuality(null);
        self::assertNull($request->getQuality());
    }

    public function provideWrongQuality(): array
    {
        return [
            ['not-valid-quality-string'],
            [588744],
            [0],
        ];
    }

    /**
     * @dataProvider provideWrongQuality
     *
     * @param int|string $quality
     */
    public function testWrongQualities($quality): void
    {
        $this->expectException(!\is_string($quality) ? \TypeError::class : InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setQuality($quality);
        if (\is_string($quality)) {
            $this->expectExceptionMessageMatches('/is invalid. Use one of/');
        }
    }

    public function testSetFormat(): void
    {
        $request = new VideoEncodingRequest();
        $formats = (new \ReflectionObject($request))->getProperty('formats');
        $formats->setAccessible(true);
        $format = \array_rand(\array_flip($formats->getValue($request)), 1);

        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setTargetFormat($format));
        self::assertEquals($format, $request->getTargetFormat());
    }

    public function provideWrongFormat(): array
    {
        return [
            ['not-valid-format'],
            ['0'],
            [0],
            [1024],
        ];
    }

    /**
     * @dataProvider provideWrongFormat
     *
     * @param int|string|null $format
     */
    public function testSetWrongFormat($format): void
    {
        $this->expectException(!\is_string($format) ? \TypeError::class : InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setTargetFormat($format);
        if (\is_string($format)) {
            $this->expectExceptionMessageMatches('is invalid. Use one of');
        }
    }

    public function provideTimes(): array
    {
        return [
            ['1:2:40.535'],
            ['2:20.0'],
            ['001:02:40.535'],
            ['2:30.535'],
        ];
    }

    /**
     * @dataProvider provideTimes
     */
    public function testSetTimes(string $time): void
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setStartTime($time));
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setEndTime($time));
        self::assertEquals($time, $request->getStartTime());
        self::assertEquals($time, $request->getEndTime());

        $request->setStartTime(null);
        $request->setEndTime(null);
        self::assertNull($request->getStartTime());
        self::assertNull($request->getEndTime());
    }

    public function testSetWrongStartTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setStartTime('not:a:time');
        $this->expectExceptionMessageMatches('/Time string/');
    }

    public function testSetWrongEndTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setEndTime('not:a:time');
        $this->expectExceptionMessageMatches('/Time string/');
    }

    public function testSetThumbs(): void
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setThumbs(10));
        self::assertEquals(10, $request->getThumbs());
    }

    public function testSetTooManyThumbs(): void
    {
        $request = new VideoEncodingRequest();
        $count = VideoEncodingRequest::MAX_THUMBS * 2;
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setThumbs($count));
        self::assertEquals(VideoEncodingRequest::MAX_THUMBS, $request->getThumbs());
    }
}
