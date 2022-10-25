<?php declare(strict_types=1);

namespace Uploadcare\File\ContentInfo;

use Uploadcare\Interfaces\File\ContentInfo\GeoLocationInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * GeoLocation.
 */
final class GeoLocation implements GeoLocationInterface, SerializableInterface
{
    private float $latitude;
    private float $longitude;

    public function __construct(?float $latitude = null, ?float $longitude = null)
    {
        $this->latitude = $latitude ?: .0;
        $this->longitude = $longitude ?: .0;
    }

    public static function rules(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
