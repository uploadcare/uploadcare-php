<?php


namespace Uploadcare\Serializer;


use Uploadcare\Interfaces\Serializer\SerializerInterface;

/**
 * Factory for serializer creation.
 */
class SerializerFactory
{
    /**
     * @return SerializerInterface
     */
    public static function create()
    {
        return new Serializer(new SnackCaseConverter());
    }
}
