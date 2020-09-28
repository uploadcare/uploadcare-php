<?php

namespace Uploadcare\Interfaces\Serializer;

interface SerializerFactoryInterface
{
    /**
     * @return SerializerInterface
     */
    public static function create();
}
