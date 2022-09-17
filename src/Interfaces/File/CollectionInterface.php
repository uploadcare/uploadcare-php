<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Uploadcare File Collection interface.
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 *
 * @template-extends \IteratorAggregate<TKey, T>
 * @template-extends \ArrayAccess<TKey|null, T>
 */
interface CollectionInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Class of inner elements.
     */
    public static function elementClass(): string;

    /**
     * @return true
     *
     * @psalm-param T $element
     */
    public function add($element): bool;

    public function clear(): void;

    /**
     * @psalm-param T $element
     */
    public function contains($element): bool;

    public function isEmpty(): bool;

    /**
     * @param string|int $key
     *
     * @return mixed Removed element
     *
     * @psalm-param TKey $key
     *
     * @psalm-return T|null
     */
    public function remove($key);

    /**
     * @psalm-param T $element
     */
    public function removeElement($element): bool;

    /**
     * @param string|int $key
     *
     * @psalm-param TKey $key
     *
     * @psalm-return T|null
     */
    public function get($key);

    /**
     * @return int[]|string[]
     *
     * @psalm-return TKey[]
     */
    public function getKeys(): array;

    /**
     * @psalm-return T[]
     */
    public function getValues(): array;

    /**
     * @param string|int $key
     *
     * @psalm-param TKey $key
     * @psalm-param T $value
     */
    public function set($key, $value): void;

    /**
     * @psalm-return array<TKey,T>
     */
    public function toArray(): array;

    /**
     * @psalm-return T|false
     */
    public function first();

    /**
     * @psalm-return T|false
     */
    public function last();

    /**
     * @return int|string
     *
     * @psalm-return TKey
     */
    public function key();

    /**
     * @psalm-return T|false
     */
    public function current();

    /**
     * @psalm-return T|false
     */
    public function next();

    /**
     * @return CollectionInterface a collection with the results of the filter operation
     */
    public function filter(\Closure $p): self;

    public function map(\Closure $func): self;

    /**
     * @return int|string|bool
     *
     * @psalm-param T $element
     *
     * @psalm-return TKey|false
     */
    public function indexOf($element);
}
