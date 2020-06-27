<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\GeoLocationInterface;

/**
 * GeoLocation.
 */
final class GeoLocation implements GeoLocationInterface
{
    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    public function __construct($latitude = null, $longitude = null)
    {
        $this->latitude = $latitude ?: .0;
        $this->longitude = $longitude ?: .0;
    }

    /**
     * @param float $latitude
     *
     * @return GeoLocation
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @param float $longitude
     *
     * @return GeoLocation
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @inheritDoc
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
