<?php

namespace Uploadcare\Interfaces\Serializer;

interface SerializerInterface
{
    /**
     * @param object $object
     * @param array  $context
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function serialize($object, array $context = []);

    /**
     * @param string      $string
     * @param string|null $className
     * @param array       $context
     *
     * @return object|array
     *
     * @throws \RuntimeException
     */
    public function deserialize($string, $className = null, array $context = []);
}
