<?php declare(strict_types=1);

namespace Uploadcare\File\ContentInfo;

use Uploadcare\Interfaces\File\ContentInfo\{GeoLocationInterface, ImageInfoInterface};
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Image Info.
 */
final class ImageInfo implements ImageInfoInterface, SerializableInterface
{
    private ?string $colorMode = null;
    private ?int $orientation = null;
    private ?string $format = null;
    private bool $isSequence = false;
    private ?int $height = null;
    private ?int $width = null;
    private ?GeoLocationInterface $geoLocation = null;
    private ?\DateTimeInterface $datetimeOriginal = null;

    /**
     * @var array<array-key, int>|null
     */
    private ?array $dpi = null;

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

    public function getColorMode(): ?string
    {
        return $this->colorMode;
    }

    public function setColorMode(?string $colorMode): self
    {
        $this->colorMode = $colorMode;

        return $this;
    }

    public function getOrientation(): ?int
    {
        return $this->orientation;
    }

    public function setOrientation(?int $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function isSequence(): bool
    {
        return $this->isSequence;
    }

    public function setIsSequence(bool $isSequence): self
    {
        $this->isSequence = $isSequence;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getGeoLocation(): ?GeoLocationInterface
    {
        return $this->geoLocation;
    }

    public function setGeoLocation(?GeoLocationInterface $geoLocation): self
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    public function getDatetimeOriginal(): ?\DateTimeInterface
    {
        return $this->datetimeOriginal;
    }

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

    public function setDpi(?array $dpi): self
    {
        $this->dpi = $dpi;

        return $this;
    }
}
