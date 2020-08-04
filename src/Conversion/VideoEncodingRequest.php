<?php

namespace Uploadcare\Conversion;

use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;

/**
 * Request for video encoding.
 */
class VideoEncodingRequest implements VideoEncodingRequestInterface
{
    const MAX_THUMBS = 50;
    const DEFAULT_RESIZE_MODE = 'preserve_ratio';
    const DEFAULT_END_TIME = 'end';

    /**
     * @var string[] Possible resizes for video
     */
    protected static $resizes = ['preserve_ratio', 'change_ratio', 'scale_crop', 'add_padding'];

    /**
     * @var string[] Possible qualities for video
     */
    protected static $qualities = ['normal', 'better', 'best', 'lighter', 'lightest'];

    /**
     * @var string[] Possible formats for video
     */
    protected static $formats = ['webm', 'ogg', 'mp4'];

    /**
     * @var string Time regex. Time string must be an `HHH:MM:SS.sss` or `MM:SS.sss`
     */
    protected static $timeRegex = '/(\d{1,3}:)?(\d{1,2}:)(\d{2}\.)(\d{0,3})/m';

    /**
     * @var int|null
     */
    private $horizontalSize;

    /**
     * @var int|null
     */
    private $verticalSize;

    /**
     * @var string|null
     */
    private $resizeMode;

    /**
     * @var string|null
     */
    private $quality;

    /**
     * @var string
     */
    private $format = 'mp4';

    /**
     * @var string|null
     */
    private $startTime;

    /**
     * @var string|null
     */
    private $endTime;

    /**
     * @var int
     */
    private $thumbs = 1;

    /**
     * @return int|null
     */
    public function getHorizontalSize()
    {
        return $this->horizontalSize;
    }

    /**
     * @param int|null $horizontalSize
     *
     * @return VideoEncodingRequest
     */
    public function setHorizontalSize($horizontalSize)
    {
        if ($horizontalSize === null) {
            $this->horizontalSize = $horizontalSize;

            return $this;
        }

        if (!\is_numeric($horizontalSize) || (int) $horizontalSize === 0 || ((int) $horizontalSize % 4) !== 0) {
            throw new InvalidArgumentException(\sprintf('Horizontal size must be an int divisible by 4, \'%s\' given', $horizontalSize));
        }
        $this->horizontalSize = $horizontalSize;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getVerticalSize()
    {
        return $this->verticalSize;
    }

    /**
     * @param int|null $verticalSize
     *
     * @return VideoEncodingRequest
     */
    public function setVerticalSize($verticalSize)
    {
        if ($verticalSize === null) {
            $this->verticalSize = $verticalSize;

            return $this;
        }

        if (!\is_numeric($verticalSize) || (int) $verticalSize === 0 || ((int) $verticalSize % 4) !== 0) {
            throw new InvalidArgumentException(\sprintf('Vertical size must be an int divisible by 4, \'%s\' given', $verticalSize));
        }
        $this->verticalSize = $verticalSize;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResizeMode()
    {
        return $this->resizeMode;
    }

    /**
     * @param string|null $resizeMode
     *
     * @return VideoEncodingRequest
     */
    public function setResizeMode($resizeMode)
    {
        if ($resizeMode !== null && !\array_key_exists($resizeMode, \array_flip(self::$resizes))) {
            throw new InvalidArgumentException(\sprintf('Resize mode \'%s\' is invalid. Use one of %s', $resizeMode, \implode(', ', self::$resizes)));
        }
        $this->resizeMode = $resizeMode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param string|null $quality
     *
     * @return VideoEncodingRequest
     */
    public function setQuality($quality)
    {
        if ($quality !== null && !\array_key_exists($quality, \array_flip(self::$qualities))) {
            throw new InvalidArgumentException(\sprintf('Quality \'%s\' is invalid. Use one of %s', $quality, \implode(', ', self::$qualities)));
        }
        $this->quality = $quality;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return VideoEncodingRequest
     */
    public function setFormat($format)
    {
        if (!\array_key_exists($format, \array_flip(self::$formats))) {
            throw new InvalidArgumentException(\sprintf('Format \'%s\' is invalid. Use one of %s', $format, \implode(', ', self::$formats)));
        }
        $this->format = $format;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param string|null $startTime
     *
     * @return VideoEncodingRequest
     */
    public function setStartTime($startTime)
    {
        if ($startTime !== null) {
            $this->checkTime($startTime);
        }
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param string|null $endTime
     *
     * @return VideoEncodingRequest
     */
    public function setEndTime($endTime)
    {
        if ($endTime !== null) {
            $this->checkTime($endTime);
        }
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }

    /**
     * @param int $thumbs
     *
     * @return VideoEncodingRequest
     */
    public function setThumbs($thumbs)
    {
        if ($thumbs > self::MAX_THUMBS) {
            $thumbs = self::MAX_THUMBS;
        }

        $this->thumbs = $thumbs;

        return $this;
    }

    /**
     * @param string $time
     */
    private function checkTime($time)
    {
        if (\preg_match(self::$timeRegex, $time) !== 1) {
            throw new InvalidArgumentException(\sprintf('Time string \'%s\' not valid', $time));
        }
    }
}
