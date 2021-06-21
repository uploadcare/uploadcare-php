<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;

/**
 * Request for video encoding.
 */
class VideoEncodingRequest implements VideoEncodingRequestInterface
{
    public const MAX_THUMBS = 50;
    public const DEFAULT_RESIZE_MODE = 'preserve_ratio';
    public const DEFAULT_END_TIME = 'end';

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
     * @var bool
     */
    private $throwError = false;

    /**
     * @var bool
     */
    private $store = true;

    /**
     * @return bool
     */
    public function throwError(): bool
    {
        return $this->throwError;
    }

    /**
     * @param bool $throwError
     *
     * @return $this
     */
    public function setThrowError(bool $throwError): self
    {
        $this->throwError = $throwError;

        return $this;
    }

    /**
     * @return bool
     */
    public function store(): bool
    {
        return $this->store;
    }

    /**
     * @param bool $store
     *
     * @return $this
     */
    public function setStore(bool $store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHorizontalSize(): ?int
    {
        return $this->horizontalSize;
    }

    /**
     * @param int|null $horizontalSize
     *
     * @return VideoEncodingRequest
     *
     * @throws InvalidArgumentException
     */
    public function setHorizontalSize(?int $horizontalSize): self
    {
        if ($horizontalSize === null) {
            $this->horizontalSize = $horizontalSize;

            return $this;
        }

        if ($horizontalSize === 0 || ($horizontalSize % 4) !== 0) {
            throw new InvalidArgumentException(\sprintf('Horizontal size must be an int divisible by 4, \'%s\' given', $horizontalSize));
        }
        $this->horizontalSize = $horizontalSize;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getVerticalSize(): ?int
    {
        return $this->verticalSize;
    }

    /**
     * @param int|null $verticalSize
     *
     * @return VideoEncodingRequest
     *
     * @throws InvalidArgumentException
     */
    public function setVerticalSize(?int $verticalSize): self
    {
        if ($verticalSize === null) {
            $this->verticalSize = $verticalSize;

            return $this;
        }

        if ($verticalSize === 0 || ($verticalSize % 4) !== 0) {
            throw new InvalidArgumentException(\sprintf('Vertical size must be an int divisible by 4, \'%s\' given', $verticalSize));
        }
        $this->verticalSize = $verticalSize;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResizeMode(): ?string
    {
        return $this->resizeMode;
    }

    /**
     * @param string|null $resizeMode
     *
     * @return VideoEncodingRequest
     *
     * @throws InvalidArgumentException
     */
    public function setResizeMode(?string $resizeMode): self
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
    public function getQuality(): ?string
    {
        return $this->quality;
    }

    /**
     * @param string|null $quality
     *
     * @return VideoEncodingRequest
     *
     * @throws InvalidArgumentException
     */
    public function setQuality(?string $quality): self
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
    public function getTargetFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return VideoEncodingRequest
     *
     * @throws InvalidArgumentException
     */
    public function setTargetFormat(string $format): self
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
    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    /**
     * @param string|null $startTime
     *
     * @return VideoEncodingRequest
     */
    public function setStartTime(?string $startTime): self
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
    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    /**
     * @param string|null $endTime
     *
     * @return VideoEncodingRequest
     */
    public function setEndTime(?string $endTime): self
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
    public function getThumbs(): int
    {
        return $this->thumbs;
    }

    /**
     * @param int $thumbs
     *
     * @return VideoEncodingRequest
     */
    public function setThumbs(int $thumbs): self
    {
        if ($thumbs > self::MAX_THUMBS) {
            $thumbs = self::MAX_THUMBS;
        }

        $this->thumbs = $thumbs;

        return $this;
    }

    /**
     * @param string $time
     *
     * @throws InvalidArgumentException
     */
    private function checkTime(string $time): void
    {
        if (\preg_match(self::$timeRegex, $time) !== 1) {
            throw new InvalidArgumentException(\sprintf('Time string \'%s\' not valid', $time));
        }
    }
}
