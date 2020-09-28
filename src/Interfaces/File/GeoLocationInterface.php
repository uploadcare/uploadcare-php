<?php

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
    public function getLatitude();

    /**
     * Location longitude.
     *
     * @return float
     */
    public function getLongitude();
}
