<?php

namespace Uploadcare\File;

/**
 * Group Collection.
 *
 * @psalm-template T of \Uploadcare\Interfaces\GroupInterface
 */
final class GroupCollection extends AbstractCollection
{
    /**
     * @var array<array-key, \Uploadcare\Interfaces\GroupInterface>
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @inheritDoc
     */
    protected function createFrom(array $elements)
    {
        return new static($elements);
    }

    /**
     * @inheritDoc
     */
    public static function elementClass()
    {
        return Group::class;
    }
}
