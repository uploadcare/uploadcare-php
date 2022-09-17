<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Serializer;

interface NameConverterInterface
{
    /**
     * Converts `attributeName` to `attribute_name`.
     */
    public function normalize(string $property): string;

    /**
     * Converts `attribute_name` to `attributeName`.
     */
    public function denormalize(string $property): string;
}
