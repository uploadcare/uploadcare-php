<?php

namespace Uploadcare\Conversion;

use Uploadcare\File\AbstractCollection;

/**
 * Collection for conversion requests.
 */
class ConvertedCollection extends AbstractCollection
{
    /**
     * @var array<array-key, \Uploadcare\Interfaces\Conversion\ConvertedItemInterface>
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    protected function createFrom(array $elements)
    {
        return new static($elements);
    }

    public static function elementClass()
    {
        return ConvertedItem::class;
    }
}
