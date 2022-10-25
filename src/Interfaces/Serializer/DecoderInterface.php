<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Serializer;

interface DecoderInterface
{
    /**
     * @return object|array
     */
    public function decode(string $data);
}
