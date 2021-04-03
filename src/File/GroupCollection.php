<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Group Collection.
 *
 * @psalm-template T of \Uploadcare\Interfaces\GroupInterface
 */
final class GroupCollection extends AbstractCollection
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
        return Group::class;
    }
}
