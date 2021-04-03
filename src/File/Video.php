<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\VideoInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Video.
 */
final class Video implements VideoInterface, SerializableInterface
{
    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $width;

    /**
     * @var float
     */
    private $frameRate;

    /**
     * @var int
     */
    private $bitrate;

    /**
     * @var string
     */
    private $codec;

    /**
     * @inheritDoc
     */
    public static function rules(): array
    {
        return [
            'height' => 'int',
            'width' => 'int',
            'frameRate' => 'float',
            'bitrate' => 'int',
            'codec' => 'string',
        ];
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     *
     * @return Video
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return Video
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return float
     */
    public function getFrameRate()
    {
        return $this->frameRate;
    }

    /**
     * @param float $frameRate
     *
     * @return Video
     */
    public function setFrameRate($frameRate)
    {
        $this->frameRate = $frameRate;

        return $this;
    }

    /**
     * @return int
     */
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * @param int $bitrate
     *
     * @return Video
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * @param string $codec
     *
     * @return Video
     */
    public function setCodec($codec)
    {
        $this->codec = $codec;

        return $this;
    }
}
