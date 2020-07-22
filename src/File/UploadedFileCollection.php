<?php

namespace Uploadcare\File;

/**
 * Uploaded File Collection.
 *
 * @psalm-template T of \Uploadcare\Interfaces\UploadedFileInterface
 */
final class UploadedFileCollection extends AbstractCollection
{
    /**
     * @var array<array-key, \Uploadcare\Interfaces\UploadedFileInterface>
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @inheritDoc
     */
    public static function elementClass()
    {
        return UploadedFile::class;
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
