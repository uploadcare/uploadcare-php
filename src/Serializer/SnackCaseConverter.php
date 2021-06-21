<?php declare(strict_types=1);

namespace Uploadcare\Serializer;

use Uploadcare\Interfaces\Serializer\NameConverterInterface;

/**
 * Converts `thisCaseAttribute` to `this_case_attribute` (or vice versa).
 */
class SnackCaseConverter implements NameConverterInterface
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Converts `attributeName` to `attribute_name`.
     *
     * @param string $property
     *
     * @return string
     */
    public function normalize(string $property): string
    {
        if (empty($this->attributes) || \in_array($property, $this->attributes, false)) {
            return \strtolower(\preg_replace('/[A-Z]/', '_\\0', \lcfirst($property)));
        }

        return $property;
    }

    /**
     * Converts `attribute_name` to `attributeName`.
     *
     * @param string $property
     *
     * @return string
     */
    public function denormalize(string $property): string
    {
        $camelCasedName = \preg_replace_callback('/(^|_|\.)+(.)/', static function ($match) {
            return ($match[1] === '.' ? '_' : '') . \strtoupper($match[2]);
        }, $property);
        $camelCasedName = \lcfirst($camelCasedName);

        if (!empty($this->attributes) && !\in_array($camelCasedName, $this->attributes, false)) {
            return $property;
        }

        return $camelCasedName;
    }
}
