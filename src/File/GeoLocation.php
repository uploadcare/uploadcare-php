<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\GeoLocationInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * GeoLocation.
 */
final class GeoLocation implements GeoLocationInterface, SerializableInterface
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
     * {@inheritDoc}
     */
    public static function rules(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * @param float $latitude
     *
     * @return GeoLocation
     */
    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @param float $longitude
     *
     * @return GeoLocation
     */
    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * {@inheritDoc}
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
