<?php

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
     * @param mixed $element
     *
     * @return true
     * @psalm-param T $element
     */
    public function add($element);

    /**
     * @return void
     */
    public function clear();

    /**
     * @param mixed $element
     *
     * @return bool
     * @psalm-param T $element
     */
    public function contains($element);

    /**
     * @return bool
     */
    public function isEmpty();

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
    public function removeElement($element);

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
    public function getKeys();

    /**
     * @return array
     * @psalm-return T[]
     */
    public function getValues();

    /**
     * @param string|int $key
     * @param mixed      $value
     *
     * @return void
     * @psalm-param TKey $key
     * @psalm-param T $value
     */
    public function set($key, $value);

    /**
     * @return array
     * @psalm-return array<TKey,T>
     */
    public function toArray();

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
     * @psalm-param Closure(T=):bool $p
     * @psalm-return CollectionInterface<TKey, T>
     */
    public function filter(\Closure $p);

    /**
     * @param \Closure $func
     *
     * @return CollectionInterface
     * @psalm-template U of
     * @psalm-param Closure(T=):U $func
     * @psalm-return CollectionInterface<TKey, U>
     */
    public function map(\Closure $func);

    /**
     * @param mixed $element
     *
     * @return int|string|bool
     * @psalm-param T $element
     * @psalm-return TKey|false
     */
    public function indexOf($element);
}
