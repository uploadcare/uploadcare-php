<?php declare(strict_types=1);

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
     * @var array<array-key, int>|null
     */
    private $dpi;

    public static function rules(): array
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
    public function getColorMode(): ?string
    {
        return $this->colorMode;
    }

    /**
     * @param string|null $colorMode
     *
     * @return ImageInfo
     */
    public function setColorMode(?string $colorMode): self
    {
        $this->colorMode = $colorMode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrientation(): ?int
    {
        return $this->orientation;
    }

    /**
     * @param int|null $orientation
     *
     * @return ImageInfo
     */
    public function setOrientation(?int $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     *
     * @return ImageInfo
     */
    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSequence(): bool
    {
        return $this->isSequence;
    }

    /**
     * @param bool $isSequence
     *
     * @return ImageInfo
     */
    public function setIsSequence(bool $isSequence): self
    {
        $this->isSequence = $isSequence;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return ImageInfo
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return ImageInfo
     */
    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return GeoLocationInterface|null
     */
    public function getGeoLocation(): ?GeoLocationInterface
    {
        return $this->geoLocation;
    }

    /**
     * @param GeoLocationInterface|null $geoLocation
     *
     * @return ImageInfo
     */
    public function setGeoLocation(?GeoLocationInterface $geoLocation): self
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatetimeOriginal(): ?\DateTimeInterface
    {
        return $this->datetimeOriginal;
    }

    /**
     * @param \DateTimeInterface|null $datetimeOriginal
     *
     * @return ImageInfo
     */
    public function setDatetimeOriginal(?\DateTimeInterface $datetimeOriginal): self
    {
        $this->datetimeOriginal = $datetimeOriginal;

        return $this;
    }

    /**
     * @return array<array-key, int>|null
     */
    public function getDpi(): ?array
    {
        return $this->dpi;
    }

    /**
     * @param array|null $dpi
     *
     * @return ImageInfo
     */
    public function setDpi(?array $dpi): self
    {
        $this->dpi = $dpi;

        return $this;
    }
}
