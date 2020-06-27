<?php

namespace Uploadcare\Interfaces\File;

interface ImageInfoInterface
{
    /**
     * Image color mode.
     * Enum: "RGB" "RGBA" "RGBa" "RGBX" "L" "LA" "La" "P" "PA" "CMYK" "YCbCr" "HSV" "LAB".
     *
     * @return string|null
     */
    public function getColorMode();

    /**
     * Image orientation from EXIF. \range(0, 8).
     *
     * @return string|null
     */
    public function getOrientation();

    /**
     * Image format.
     *
     * @return string|null
     */
    public function getFormat();

    /**
     * Is image if sequence image.
     *
     * @return bool
     */
    public function isSequence();

    /**
     * Image height in pixels.
     *
     * @return int|null
     */
    public function getHeight();

    /**
     * Image width in pixels.
     *
     * @return int|null
     */
    public function getWidth();

    /**
     * Geo-location of image from EXIF.
     *
     * @return GeoLocationInterface|null
     */
    public function getGeoLocation();

    /**
     * Image date and time from EXIF.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeOriginal();

    /**
     * Image DPI for two dimensions.
     *
     * @return null|array<array-key, int>
     */
    public function getDpi();
}
