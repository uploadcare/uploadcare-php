<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

interface SerializableInterface
{
    /**
     * Serialization rules.
     * Must return an array with class property names in keys and class property types in values. If property type
     * is a related class, the fully qualified class name must be the value of array element, and related class
     * must implements this interface or must be the one of \DateTimeInterface subclasses.
     *
     * @return array|string[]
     */
    public static function rules(): array;
}
