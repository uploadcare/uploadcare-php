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
     * @param string      $string    Data string
     * @param string|null $className Class name fot serialize to or null for return array
     * @param array       $context   Any serialization context
     *
     * @return object|array
     *
     * @throws \RuntimeException
     */
    public function deserialize($string, $className = null, array $context = []);
}
