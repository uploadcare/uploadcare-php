<?php

namespace Uploadcare\Interfaces\Serializer;

interface DecoderInterface
{
    /**
     * @param string $data
     *
     * @return object|array
     */
    public function decode($data);
}
