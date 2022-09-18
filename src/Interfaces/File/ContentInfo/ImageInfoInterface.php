<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\ContentInfo;

interface ImageInfoInterface
{
    /**
     * Image color mode.
     * Enum: "RGB" "RGBA" "RGBa" "RGBX" "L" "LA" "La" "P" "PA" "CMYK" "YCbCr" "HSV" "LAB".
     */
    public function getColorMode(): ?string;

    /**
     * Image orientation from EXIF. \range(0, 8).
     */
    public function getOrientation(): ?int;

    /**
     * Image format.
     */
    public function getFormat(): ?string;

    /**
     * Is image if sequence image.
     */
    public function isSequence(): bool;

    /**
     * Image height in pixels.
     */
    public function getHeight(): ?int;

    /**
     * Image width in pixels.
     */
    public function getWidth(): ?int;

    /**
     * Geolocation of image from EXIF.
     */
    public function getGeoLocation(): ?GeoLocationInterface;

    /**
     * Image date and time from EXIF.
     */
    public function getDatetimeOriginal(): ?\DateTimeInterface;

    /**
     * Image DPI for two dimensions.
     *
     * @return array<array-key, int>|null
     */
    public function getDpi(): ?array;
}
