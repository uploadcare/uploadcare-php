<?php

namespace Uploadcare\Conversion;

use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Collection for conversion requests.
 * @psalm-template T of \Uploadcare\Interfaces\Conversion\ConvertedItemInterface
 */
final class ConvertedCollection extends AbstractCollection
{
    /**
     * @var array<array-key,T>
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    protected function createFrom(array $elements): CollectionInterface
    {
        return new static($elements);
    }

    public static function elementClass(): string
    {
        return ConvertedItem::class;
    }
}
