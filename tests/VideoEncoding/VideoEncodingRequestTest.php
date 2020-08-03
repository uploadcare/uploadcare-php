<?php

namespace Tests\VideoEncoding;

use PHPUnit\Framework\TestCase;
use Uploadcare\Conversion\VideoEncodingRequest;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;

class VideoEncodingRequestTest extends TestCase
{
    public function testSetHorizontalSize()
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setHorizontalSize(640));
        self::assertEquals(640, $request->getHorizontalSize());
        $request->setHorizontalSize(null);
        self::assertNull($request->getHorizontalSize());
    }

    public function testSetVerticalSize()
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setVerticalSize(480));
        self::assertEquals(480, $request->getVerticalSize());
        $request->setVerticalSize(null);
        self::assertNull($request->getVerticalSize());
    }

    public function provideWrongSizes()
    {
        return [
            ['not-an-int'],
            [473],
            [0],
        ];
    }

    /**
     * @dataProvider provideWrongSizes
     *
     * @param int|string $size
     */
    public function testWrongHorizontalSize($size)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setHorizontalSize($size);
        $this->expectExceptionMessageRegExp('Horizontal size must be an int divisible by 4');
    }

    /**
     * @dataProvider provideWrongSizes
     *
     * @param int|string $size
     */
    public function testWrongVerticalSize($size)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setVerticalSize($size);
        $this->expectExceptionMessageRegExp('Vertical size must be an int divisible by 4');
    }

    public function testSetResizeMode()
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

    public function provideWrongResizeModes()
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
    public function testWrongResizeModes($mode)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setResizeMode($mode);
        $this->expectExceptionMessageRegExp('is invalid. Use one of');
    }

    public function testSetQuality()
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

    public function provideWrongQuality()
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
    public function testWrongQualities($quality)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setQuality($quality);
        $this->expectExceptionMessageRegExp('is invalid. Use one of');
    }

    public function testSetFormat()
    {
        $request = new VideoEncodingRequest();
        $formats = (new \ReflectionObject($request))->getProperty('formats');
        $formats->setAccessible(true);
        $format = \array_rand(\array_flip($formats->getValue($request)), 1);

        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setFormat($format));
        self::assertEquals($format, $request->getFormat());
    }

    public function provideWrongFormat()
    {
        return [
            ['not-valid-format'],
            ['0'],
            [0],
            [1024],
            [null],
        ];
    }

    /**
     * @dataProvider provideWrongFormat
     *
     * @param int|string|null $format
     */
    public function testSetWrongFormat($format)
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setFormat($format);
        $this->expectExceptionMessageRegExp('is invalid. Use one of');
    }

    public function provideTimes()
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
     *
     * @param string $time
     */
    public function testSetTimes($time)
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

    public function testSetWrongStartTime()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setStartTime('not:a:time');
        $this->expectExceptionMessageRegExp('Time string');
    }

    public function testSetWrongEndTime()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new VideoEncodingRequest();
        $request->setEndTime('not:a:time');
        $this->expectExceptionMessageRegExp('Time string');
    }

    public function testSetThumbs()
    {
        $request = new VideoEncodingRequest();
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setThumbs(10));
        self::assertEquals(10, $request->getThumbs());
    }

    public function testSetTooManyThumbs()
    {
        $request = new VideoEncodingRequest();
        $count = VideoEncodingRequest::MAX_THUMBS * 2;
        self::assertInstanceOf(VideoEncodingRequestInterface::class, $request->setThumbs($count));
        self::assertEquals(VideoEncodingRequest::MAX_THUMBS, $request->getThumbs());
    }
}
