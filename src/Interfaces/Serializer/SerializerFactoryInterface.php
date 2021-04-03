<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Serializer;

interface SerializerFactoryInterface
{
    public static function create(): SerializerInterface;
}
