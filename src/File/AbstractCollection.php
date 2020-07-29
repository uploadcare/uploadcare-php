<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Abstract Collection.
 * Contains common collection methods.
 *
 * @psalm-template T of object
 */
abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var array
     */
    protected $elements;

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]) || \array_key_exists($offset, $this->elements);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        if (!isset($this->elements[$key]) && !\array_key_exists($key, $this->elements)) {
            return null;
        }

        return $this->elements[$key];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function add($element)
    {
        $this->elements[] = $element;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        $this->elements[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function remove($key)
    {
        if (!isset($this->elements[$key]) && !\array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->elements = [];
    }

    /**
     * @inheritDoc
     */
    public function contains($element)
    {
        return \in_array($element, $this->elements, true);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return empty($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function removeElement($element)
    {
        $key = \array_search($element, $this->elements, true);
        if ($key === false) {
            return false;
        }
        unset($this->elements[$key]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getKeys()
    {
        return \array_keys($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return \array_values($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return \reset($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        return \end($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return \key($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return \current($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        return \next($this->elements);
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function filter(\Closure $p)
    {
        return $this->createFrom(\array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param array $elements
     *
     * @return self
     * @psalm-param array<array-key,T> $elements
     */
    abstract protected function createFrom(array $elements);

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function map(\Closure $func)
    {
        return $this->createFrom(\array_map($func, $this->elements));
    }

    /**
     * @inheritDoc
     */
    public function indexOf($element)
    {
        return \array_search($element, $this->elements, true);
    }
}
