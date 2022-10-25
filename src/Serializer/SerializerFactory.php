<?php declare(strict_types=1);

namespace Uploadcare\Serializer;

use Uploadcare\Interfaces\Serializer\SerializerInterface;

/**
 * Factory for serializer creation.
 */
class SerializerFactory
{
    public static function create(): SerializerInterface
    {
        return new Serializer(new SnackCaseConverter());
    }
}
