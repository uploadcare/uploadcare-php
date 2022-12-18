<?php declare(strict_types=1);

namespace Uploadcare\File;

use ArrayAccess;
use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Abstract Collection.
 * Contains common collection methods.
 *
 * @psalm-template TKey of mixed
 * @psalm-template T
 */
abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var array<TKey,T>
     */
    protected array $elements = [];

    /**
     * @psalm-return \Traversable
     */
    public function getIterator(): \Traversable
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

    /**
     * @param string|int $offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
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

    /**
     * @psalm-param TKey|null $offset
     * @psalm-param T $value
     *
     * @psalm-suppress ImplementedParamTypeMismatch
     */
    public function offsetSet($offset, $value): void
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * @param mixed $element the element to add
     *
     * @psalm-param T $element
     *
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
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|int $key   the key/index of the element to set
     * @param mixed      $value the element to set
     *
     * @psalm-param TKey $key
     * @psalm-param T $value
     */
    public function set($key, $value): void
    {
        $this->elements[$key] = $value;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param TKey $offset
     *
     * @psalm-suppress ImplementedParamTypeMismatch
     */
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
