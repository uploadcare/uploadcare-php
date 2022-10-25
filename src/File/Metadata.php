<?php declare(strict_types=1);

namespace Uploadcare\File;

final class Metadata implements \ArrayAccess, \Countable, \IteratorAggregate
{
    private static string $validationRegex = '/[\w\-\.\:]+/';

    /**
     * @var array<string, string>
     */
    private array $elements;

    public function __construct(array $elements = [])
    {
        $elements = \array_filter($elements, static fn ($value, $key) => \is_string($key) && \is_string($value), ARRAY_FILTER_USE_BOTH);

        $this->elements = $elements;
    }

    /**
     * @psalm-param mixed $key
     */
    public static function validateKey($key): bool
    {
        if (!\is_string($key) || \strlen($key) > 64) {
            return false;
        }
        $num = \preg_match_all(self::$validationRegex, $key);

        return $num === 1;
    }

    public function offsetExists($offset): bool
    {
        return ($this->elements[$offset] ?? null) !== null;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->elements[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            return;
        }

        $this->elements[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        if ($offset === null) {
            return;
        }

        if (!$this->offsetExists($offset)) {
            return;
        }

        unset($this->elements[$offset]);
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    public function getIterator(): \Traversable
    {
        if (\count($this->elements) === 0) {
            return new \EmptyIterator();
        }

        return new \ArrayIterator($this->elements);
    }
}
