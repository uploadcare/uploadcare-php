<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Collection for conversion requests.
 *
 * @psalm-template T of \Uploadcare\Interfaces\Conversion\ConvertedItemInterface
 */
final class ConvertedCollection extends AbstractCollection
{
    /**
     * @var array<int, T>
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    protected function createFrom(array $elements): CollectionInterface
    {
        return new self($elements);
    }

    public static function elementClass(): string
    {
        return ConvertedItem::class;
    }
}
