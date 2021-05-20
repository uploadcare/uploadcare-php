<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Uploadcare File Collection interface.
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends IteratorAggregate<TKey, T>
 * @template-extends ArrayAccess<TKey|null, T>
 */
interface CollectionInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Class of inner elements.
     *
     * @return string
     */
    public static function elementClass(): string;

    /**
     * @param mixed $element
     *
     * @return true
     * @psalm-param T $element
     */
    public function add($element): bool;

    /**
     * @return void
     */
    public function clear(): void;

    /**
     * @param mixed $element
     *
     * @return bool
     * @psalm-param T $element
     */
    public function contains($element): bool;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param string|int $key
     *
     * @return mixed Removed element
     * @psalm-param TKey $key
     * @psalm-return T|null
     */
    public function remove($key);

    /**
     * @param mixed $element
     *
     * @return bool
     * @psalm-param T $element
     */
    public function removeElement($element): bool;

    /**
     * @param string|int $key
     *
     * @return mixed
     * @psalm-param TKey $key
     * @psalm-return T|null
     */
    public function get($key);

    /**
     * @return int[]|string[]
     * @psalm-return TKey[]
     */
    public function getKeys(): array;

    /**
     * @return array
     * @psalm-return T[]
     */
    public function getValues(): array;

    /**
     * @param string|int $key
     * @param mixed      $value
     *
     * @return void
     * @psalm-param TKey $key
     * @psalm-param T $value
     */
    public function set($key, $value): void;

    /**
     * @return array
     * @psalm-return array<TKey,T>
     */
    public function toArray(): array;

    /**
     * @return mixed
     * @psalm-return T|false
     */
    public function first();

    /**
     * @return mixed
     * @psalm-return T|false
     */
    public function last();

    /**
     * @return int|string
     * @psalm-return TKey
     */
    public function key();

    /**
     * @return mixed
     * @psalm-return T|false
     */
    public function current();

    /**
     * @return mixed
     * @psalm-return T|false
     */
    public function next();

    /**
     * @param \Closure $p
     *
     * @return CollectionInterface a collection with the results of the filter operation
     */
    public function filter(\Closure $p): self;

    /**
     * @param \Closure $func
     *
     * @return CollectionInterface
     */
    public function map(\Closure $func): self;

    /**
     * @param mixed $element
     *
     * @return int|string|bool
     * @psalm-param T $element
     * @psalm-return TKey|false
     */
    public function indexOf($element);
}
