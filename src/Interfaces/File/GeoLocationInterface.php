<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Geo-location of image from EXIF.
 */
interface GeoLocationInterface
{
    /**
     * Location latitude.
     *
     * @return float
     */
    public function getLatitude(): float;

    /**
     * Location longitude.
     *
     * @return float
     */
    public function getLongitude(): float;
}
