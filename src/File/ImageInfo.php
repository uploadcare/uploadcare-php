<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\GeoLocationInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Image Info.
 */
final class ImageInfo implements ImageInfoInterface, SerializableInterface
{
    /**
     * @var string|null
     */
    private $colorMode;

    /**
     * @var int|null
     */
    private $orientation;

    /**
     * @var string|null
     */
    private $format;

    /**
     * @var bool
     */
    private $isSequence;

    /**
     * @var int|null
     */
    private $height;

    /**
     * @var int|null
     */
    private $width;

    /**
     * @var GeoLocationInterface|null
     */
    private $geoLocation;

    /**
     * @var \DateTimeInterface|null
     */
    private $datetimeOriginal;

    /**
     * @var null|array<array-key, int>
     */
    private $dpi;

    public static function rules()
    {
        return [
            'colorMode' => 'string',
            'orientation' => 'int',
            'format' => 'string',
            'isSequence' => 'bool',
            'height' => 'int',
            'width' => 'int',
            'geoLocation' => GeoLocation::class,
            'datetimeOriginal' => \DateTime::class,
            'dpi' => 'array',
        ];
    }

    /**
     * @return string|null
     */
    public function getColorMode()
    {
        return $this->colorMode;
    }

    /**
     * @param string|null $colorMode
     *
     * @return ImageInfo
     */
    public function setColorMode($colorMode)
    {
        $this->colorMode = $colorMode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param int|null $orientation
     *
     * @return ImageInfo
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     *
     * @return ImageInfo
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSequence()
    {
        return (bool) $this->isSequence;
    }

    /**
     * @param bool $isSequence
     *
     * @return ImageInfo
     */
    public function setIsSequence($isSequence)
    {
        $this->isSequence = (bool) $isSequence;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return ImageInfo
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return ImageInfo
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return GeoLocationInterface|null
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param GeoLocationInterface|null $geoLocation
     *
     * @return ImageInfo
     */
    public function setGeoLocation($geoLocation)
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatetimeOriginal()
    {
        return $this->datetimeOriginal;
    }

    /**
     * @param \DateTimeInterface|null $datetimeOriginal
     *
     * @return ImageInfo
     */
    public function setDatetimeOriginal($datetimeOriginal)
    {
        $this->datetimeOriginal = $datetimeOriginal;

        return $this;
    }

    /**
     * @return null|array<array-key, int>
     */
    public function getDpi()
    {
        return $this->dpi;
    }

    /**
     * @param array|null $dpi
     *
     * @return ImageInfo
     */
    public function setDpi($dpi)
    {
        $this->dpi = $dpi;

        return $this;
    }
}
