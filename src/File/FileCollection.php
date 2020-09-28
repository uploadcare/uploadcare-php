<?php

namespace Uploadcare\File;

/**
 * File Collection.
 *
 * @psalm-template T of \Uploadcare\Interfaces\File\FileInfoInterface
 */
final class FileCollection extends AbstractCollection
{
    /**
     * @var array<array-key, \Uploadcare\Interfaces\File\FileInfoInterface>
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public static function elementClass()
    {
        return File::class;
    }

    /**
     * @param array $elements
     *
     * @return $this
     */
    protected function createFrom(array $elements)
    {
        return new static($elements);
    }
}
