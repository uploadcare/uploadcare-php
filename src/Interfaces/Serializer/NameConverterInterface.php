<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Serializer;

interface NameConverterInterface
{
    /**
     * Converts `attributeName` to `attribute_name`.
     *
     * @param string $property
     *
     * @return string
     */
    public function normalize(string $property): string;

    /**
     * Converts `attribute_name` to `attributeName`.
     *
     * @param string $property
     *
     * @return string
     */
    public function denormalize(string $property): string;
}
