<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

interface ImageInfoInterface
{
    /**
     * Image color mode.
     * Enum: "RGB" "RGBA" "RGBa" "RGBX" "L" "LA" "La" "P" "PA" "CMYK" "YCbCr" "HSV" "LAB".
     *
     * @return string|null
     */
    public function getColorMode(): ?string;

    /**
     * Image orientation from EXIF. \range(0, 8).
     *
     * @return int|null
     */
    public function getOrientation(): ?int;

    /**
     * Image format.
     *
     * @return string|null
     */
    public function getFormat(): ?string;

    /**
     * Is image if sequence image.
     *
     * @return bool
     */
    public function isSequence(): bool;

    /**
     * Image height in pixels.
     *
     * @return int|null
     */
    public function getHeight(): ?int;

    /**
     * Image width in pixels.
     *
     * @return int|null
     */
    public function getWidth(): ?int;

    /**
     * Geo-location of image from EXIF.
     *
     * @return GeoLocationInterface|null
     */
    public function getGeoLocation(): ?GeoLocationInterface;

    /**
     * Image date and time from EXIF.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeOriginal(): ?\DateTimeInterface;

    /**
     * Image DPI for two dimensions.
     *
     * @return array<array-key, int>|null
     */
    public function getDpi(): ?array;
}
