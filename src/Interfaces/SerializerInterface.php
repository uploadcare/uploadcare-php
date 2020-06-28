<?php

namespace Uploadcare\Interfaces;

interface SerializerInterface
{
    /**
     * @param object $object
     * @param string $format
     * @param array  $context
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function normalize($object, $format = 'json', array $context = []);

    /**
     * @param string      $string
     * @param string|null $className
     *
     * @return object|array
     */
    public function denormalize($string, $className = null);
}
