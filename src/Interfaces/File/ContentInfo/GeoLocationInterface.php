<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\ContentInfo;

/**
 * Geolocation of image from EXIF.
 */
interface GeoLocationInterface
{
    /**
     * Location latitude.
     */
    public function getLatitude(): float;

    /**
     * Location longitude.
     */
    public function getLongitude(): float;
}
