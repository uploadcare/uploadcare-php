<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Abstract Collection.
 * Contains common collection methods.
 *
 * @psalm-template TKey of int
 * @psalm-template T
 */
abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var array<TKey,T>
     */
    protected $elements = [];

    /**
     * @return \Traversable
     */
    public function getIterator(): iterable
    {
        if (\count($this->elements) === 0) {
            return new \EmptyIterator();
        }

        return new \ArrayIterator($this->elements);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]) || \array_key_exists($offset, $this->elements);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function get($key)
    {
        if (!isset($this->elements[$key]) && !\array_key_exists($key, $this->elements)) {
            return null;
        }

        return $this->elements[$key];
    }

    public function offsetSet($offset, $value): void
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * @param mixed $element
     * @psalm-suppress InvalidPropertyAssignmentValue
     *
     * @return true
     */
    public function add($element): bool
    {
        $this->elements[] = $element;

        return true;
    }

    /**
     * @param int|string $key
     * @param mixed      $value
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    public function set($key, $value): void
    {
        $this->elements[$key] = $value;
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    public function remove($key)
    {
        if (!isset($this->elements[$key]) && !\array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    public function contains($element): bool
    {
        return \in_array($element, $this->elements, true);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function removeElement($element): bool
    {
        $key = \array_search($element, $this->elements, true);
        if ($key === false) {
            return false;
        }
        unset($this->elements[$key]);

        return true;
    }

    public function getKeys(): array
    {
        return \array_keys($this->elements);
    }

    public function getValues(): array
    {
        return \array_values($this->elements);
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    public function first()
    {
        return \reset($this->elements);
    }

    public function last()
    {
        return \end($this->elements);
    }

    public function key()
    {
        return \key($this->elements);
    }

    public function current()
    {
        return \current($this->elements);
    }

    public function next()
    {
        return \next($this->elements);
    }

    public function filter(\Closure $p): CollectionInterface
    {
        return $this->createFrom(\array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    abstract protected function createFrom(array $elements): CollectionInterface;

    public function map(\Closure $func): CollectionInterface
    {
        return $this->createFrom(\array_map($func, $this->elements));
    }

    public function indexOf($element)
    {
        return \array_search($element, $this->elements, true);
    }
}
