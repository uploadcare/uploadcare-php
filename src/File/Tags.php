<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Exception\TagsException;

/**
 * Ordered list of searchable string tags attached to a file.
 *
 * @see https://uploadcare.com/docs/file-tags/
 */
final class Tags implements \Countable, \IteratorAggregate, \Stringable
{
    public const MAX_TAGS = 50;
    public const MAX_TAG_LENGTH = 100;

    private static string $validationRegex = '/^[A-Za-z0-9._\-]+$/';

    /**
     * @var array<int, string>
     */
    private array $tags;

    /**
     * @param array<array-key, mixed> $tags
     */
    public function __construct(array $tags = [])
    {
        $this->tags = self::normalize($tags);
    }

    /**
     * Whether a single tag is acceptable for the API.
     *
     * Validation is made against the normalized value, so ' Cat ' is a valid tag.
     *
     * @psalm-param mixed $tag
     */
    public static function validateTag($tag): bool
    {
        if (!\is_string($tag)) {
            return false;
        }

        $tag = self::normalizeTag($tag);
        if ($tag === '' || \strlen($tag) > self::MAX_TAG_LENGTH) {
            return false;
        }

        return \preg_match(self::$validationRegex, $tag) === 1;
    }

    /**
     * Apply the normalization rules of the API: lowercase, strip whitespace, discard empty
     * values and remove duplicates keeping the first occurrence. Order of first appearance
     * is preserved. Values that are not strings are discarded.
     *
     * @param array<array-key, mixed> $tags
     *
     * @return array<int, string>
     */
    public static function normalize(array $tags): array
    {
        $result = [];
        foreach ($tags as $tag) {
            if (!\is_string($tag)) {
                continue;
            }

            $tag = self::normalizeTag($tag);
            if ($tag === '' || \in_array($tag, $result, true)) {
                continue;
            }

            $result[] = $tag;
        }

        return $result;
    }

    /**
     * Normalize the given tags and make sure the API will accept them.
     *
     * Both limits are evaluated after normalization, the same way the API does it.
     *
     * @param array<array-key, mixed> $tags
     *
     * @return array<int, string> Normalized tags
     *
     * @throws TagsException
     */
    public static function assertValid(array $tags): array
    {
        foreach ($tags as $tag) {
            // Empty values are discarded by normalization, not an error.
            if (\is_string($tag) && self::normalizeTag($tag) === '') {
                continue;
            }

            if (self::validateTag($tag) === false) {
                throw new TagsException(\sprintf('Tag should be a string up to %d characters length. Allowed symbols are A-z, 0-9, hyphen, underscore and dot. Got \'%s\'.', self::MAX_TAG_LENGTH, \is_scalar($tag) ? (string) $tag : \gettype($tag)));
            }
        }

        $normalized = self::normalize($tags);
        if (\count($normalized) > self::MAX_TAGS) {
            throw new TagsException(\sprintf('Up to %d tags per file allowed, %d given.', self::MAX_TAGS, \count($normalized)));
        }

        return $normalized;
    }

    /**
     * Tags in storage order.
     *
     * @return array<int, string>
     */
    public function toArray(): array
    {
        return $this->tags;
    }

    /**
     * Whether the file has the given tag. The argument is normalized before comparison,
     * so `has('Cat')` matches the stored `cat`.
     */
    public function has(string $tag): bool
    {
        return \in_array(self::normalizeTag($tag), $this->tags, true);
    }

    public function count(): int
    {
        return \count($this->tags);
    }

    public function getIterator(): \Traversable
    {
        if (\count($this->tags) === 0) {
            return new \EmptyIterator();
        }

        return new \ArrayIterator($this->tags);
    }

    /**
     * Comma-separated list, the format the Upload API expects in the `tags` form field.
     */
    public function __toString(): string
    {
        return \implode(',', $this->tags);
    }

    private static function normalizeTag(string $tag): string
    {
        return \strtolower(\trim($tag));
    }
}
