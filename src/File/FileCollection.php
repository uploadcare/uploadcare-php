<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * File Collection.
 *
 * @psalm-template T of \Uploadcare\Interfaces\File\FileInfoInterface
 */
final class FileCollection extends AbstractCollection
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

    public static function elementClass(): string
    {
        return File::class;
    }

    protected function createFrom(array $elements): CollectionInterface
    {
        return new FileCollection($elements);
    }
}
