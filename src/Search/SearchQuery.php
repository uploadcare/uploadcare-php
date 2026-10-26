<?php declare(strict_types=1);

namespace Uploadcare\Search;

use Uploadcare\Exception\SearchException;
use Uploadcare\File\Tags;

/**
 * Search conditions for the `POST /files/search/` request.
 *
 * Different conditions are combined with AND. The only ways to express OR are multiple values of
 * one `exact` field, `query` across its fields and `tagsAny()`; the only NOT is `tagsNone()`.
 *
 * ```php
 * $query = (new SearchQuery())
 *     ->phrase(SearchQuery::FIELD_ORIGINAL_FILENAME, 'invoice')
 *     ->exact(SearchQuery::FIELD_MIME_TYPE, ['application/pdf'])
 *     ->datetimeUploaded(SearchQuery::GTE, new \DateTimeImmutable('2024-01-01'))
 *     ->sort('-datetime_uploaded');
 * ```
 *
 * @see https://uploadcare.com/docs/file-search/
 */
final class SearchQuery implements \JsonSerializable
{
    public const FIELD_ORIGINAL_FILENAME = 'original_filename';
    public const FIELD_MIME_TYPE = 'detected_mime_type';
    public const FIELD_METADATA = 'metadata';
    public const FIELD_UUID = 'uuid';

    public const GT = 'gt';
    public const GTE = 'gte';
    public const LT = 'lt';
    public const LTE = 'lte';

    public const SORT_SCORE = 'score';
    public const SORT_DATETIME_UPLOADED = 'datetime_uploaded';
    public const SORT_SIZE = 'size';
    public const SORT_ORIGINAL_FILENAME = 'original_filename';

    public const MIN_TERM_LENGTH = 4;
    public const MAX_SORT_KEYS = 4;

    private const PHRASE_FIELDS = [self::FIELD_ORIGINAL_FILENAME, self::FIELD_MIME_TYPE, self::FIELD_METADATA];
    private const EXACT_FIELDS = [self::FIELD_ORIGINAL_FILENAME, self::FIELD_MIME_TYPE, self::FIELD_UUID];
    private const OPERATORS = [self::GT, self::GTE, self::LT, self::LTE];
    private const SORT_KEYS = [self::SORT_SCORE, self::SORT_DATETIME_UPLOADED, self::SORT_SIZE, self::SORT_ORIGINAL_FILENAME];

    private ?string $query = null;

    /**
     * @var array<string, string>
     */
    private array $phrase = [];

    /**
     * @var array<string, array<int, string>>
     */
    private array $exact = [];

    /**
     * @var array<string, string>
     */
    private array $datetimeUploaded = [];

    /**
     * @var array<string, int>
     */
    private array $size = [];

    private ?bool $isImage = null;

    /**
     * @var array<string, array<int, string>>
     */
    private array $tags = [];

    private ?bool $fuzziness = null;

    /**
     * @var array<int, string>
     */
    private array $sort = [];

    /**
     * Full-text search across filename, metadata and MIME type at once. A file UUID in the term
     * puts that file at the top of the results.
     *
     * @throws SearchException if the term is shorter than 4 characters
     */
    public function query(string $query): self
    {
        $this->query = self::assertTerm($query, 'query');

        return $this;
    }

    /**
     * Full-text search scoped to one field. Calling it for several fields combines them with AND.
     *
     * @param string $field One of `original_filename`, `detected_mime_type` or `metadata` (all metadata keys and values)
     *
     * @throws SearchException
     */
    public function phrase(string $field, string $value): self
    {
        if (!\in_array($field, self::PHRASE_FIELDS, true)) {
            throw new SearchException(\sprintf('Field \'%s\' is not supported in phrase. Supported fields are %s.', $field, \implode(', ', self::PHRASE_FIELDS)));
        }

        $this->phrase[$field] = self::assertTerm($value, \sprintf('phrase.%s', $field));

        return $this;
    }

    /**
     * Exact value matching. Several values of one field are combined with OR, several fields with AND.
     * Calling it again for the same field replaces its values.
     *
     * @param string                          $field  One of `original_filename`, `detected_mime_type` or `uuid`. Use `exactMetadata()` for a metadata key.
     * @param string|array<array-key, string> $values
     *
     * @throws SearchException
     */
    public function exact(string $field, $values): self
    {
        if (!\in_array($field, self::EXACT_FIELDS, true)) {
            throw new SearchException(\sprintf('Field \'%s\' is not supported in exact. Supported fields are %s, use exactMetadata() for metadata keys.', $field, \implode(', ', self::EXACT_FIELDS)));
        }

        $this->exact[$field] = self::assertValues($values, \sprintf('exact.%s', $field));

        return $this;
    }

    /**
     * Exact match of a single metadata key. Several values are combined with OR.
     *
     * @param string|array<array-key, string> $values
     *
     * @throws SearchException
     */
    public function exactMetadata(string $key, $values): self
    {
        if ($key === '') {
            throw new SearchException('Metadata key should not be empty.');
        }

        $field = \sprintf('%s[%s]', self::FIELD_METADATA, $key);
        $this->exact[$field] = self::assertValues($values, \sprintf('exact.%s', $field));

        return $this;
    }

    /**
     * Upload date filter. Call it twice to make a range, e.g. `GTE` and `LT`.
     *
     * @param string $operator One of `gt`, `gte`, `lt` or `lte`
     *
     * @throws SearchException on unknown operator
     */
    public function datetimeUploaded(string $operator, \DateTimeInterface $value): self
    {
        $utc = \DateTimeImmutable::createFromFormat('U.u', $value->format('U.u'));
        if ($utc === false) {
            throw new SearchException('Unable to convert the upload date to UTC.');
        }

        $this->datetimeUploaded[self::assertOperator($operator, 'datetime_uploaded')] = $utc->format('Y-m-d\TH:i:s\Z');

        return $this;
    }

    /**
     * File size filter in bytes. Call it twice to make a range, e.g. `GTE` and `LTE`.
     *
     * @param string $operator One of `gt`, `gte`, `lt` or `lte`
     *
     * @throws SearchException on unknown operator or negative size
     */
    public function size(string $operator, int $bytes): self
    {
        if ($bytes < 0) {
            throw new SearchException(\sprintf('Size should not be negative, %d given.', $bytes));
        }

        $this->size[self::assertOperator($operator, 'size')] = $bytes;

        return $this;
    }

    /**
     * `true` returns images only, `false` excludes images.
     */
    public function isImage(bool $isImage = true): self
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * File must have at least one of these tags.
     *
     * @param array<array-key, string> $tags
     *
     * @throws \Uploadcare\Exception\TagsException on invalid tags
     */
    public function tagsAny(array $tags): self
    {
        return $this->setTags('any', $tags);
    }

    /**
     * File must have all of these tags.
     *
     * @param array<array-key, string> $tags
     *
     * @throws \Uploadcare\Exception\TagsException on invalid tags
     */
    public function tagsAll(array $tags): self
    {
        return $this->setTags('all', $tags);
    }

    /**
     * File must have none of these tags.
     *
     * @param array<array-key, string> $tags
     *
     * @throws \Uploadcare\Exception\TagsException on invalid tags
     */
    public function tagsNone(array $tags): self
    {
        return $this->setTags('none', $tags);
    }

    /**
     * Typo-tolerant matching for `query` and `phrase`. Mind that it significantly increases latency.
     */
    public function fuzziness(bool $fuzziness = true): self
    {
        $this->fuzziness = $fuzziness;

        return $this;
    }

    /**
     * 1 to 4 sort keys, prefix a key with `-` for descending order. Without sort, results are
     * ordered by relevance, and for filter-only queries the order is undefined.
     *
     * @param string ...$keys `score`, `datetime_uploaded`, `size` or `original_filename`, optionally prefixed with `-`
     *
     * @throws SearchException
     */
    public function sort(string ...$keys): self
    {
        if (\count($keys) === 0 || \count($keys) > self::MAX_SORT_KEYS) {
            throw new SearchException(\sprintf('From 1 to %d sort keys allowed, %d given.', self::MAX_SORT_KEYS, \count($keys)));
        }

        $seen = [];
        foreach ($keys as $key) {
            $name = \ltrim($key, '-');
            if (!\in_array($name, self::SORT_KEYS, true) || \strlen($key) - \strlen($name) > 1) {
                throw new SearchException(\sprintf('Sort key \'%s\' is not supported. Supported keys are %s, optionally prefixed with \'-\'.', $key, \implode(', ', self::SORT_KEYS)));
            }
            if (isset($seen[$name])) {
                throw new SearchException('Sort keys must be unique.');
            }
            $seen[$name] = true;
        }

        $this->sort = \array_values($keys);

        return $this;
    }

    /**
     * Request body.
     *
     * @throws SearchException when the query has no conditions or a field is used in both phrase and exact
     */
    public function toArray(): array
    {
        $conflicts = \array_keys(\array_intersect_key($this->phrase, $this->exact));
        if ($conflicts !== []) {
            throw new SearchException(\sprintf('Fields cannot appear in both phrase and exact: %s.', \implode(', ', $conflicts)));
        }

        $body = \array_filter([
            'query' => $this->query,
            'phrase' => $this->phrase,
            'exact' => $this->exact,
            'datetime_uploaded' => $this->datetimeUploaded,
            'size' => $this->size,
            'is_image' => $this->isImage,
            'tags' => $this->tags,
        ], static fn ($value): bool => $value !== null && $value !== []);

        if ($body === []) {
            throw new SearchException('At least one search criterion must be specified.');
        }

        if ($this->fuzziness !== null) {
            $body['fuzziness'] = $this->fuzziness;
        }
        if ($this->sort !== []) {
            $body['sort'] = $this->sort;
        }

        return $body;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param array<array-key, string> $tags
     */
    private function setTags(string $key, array $tags): self
    {
        // The same rules as for writing tags, except the per-file limit.
        foreach ($tags as $tag) {
            Tags::assertValid([$tag]);
        }

        $normalized = Tags::normalize($tags);
        if ($normalized === []) {
            unset($this->tags[$key]);
        } else {
            $this->tags[$key] = $normalized;
        }

        return $this;
    }

    private static function assertTerm(string $value, string $name): string
    {
        // Counts characters, not bytes, without depending on mbstring.
        if ((int) \preg_match_all('/./su', \trim($value)) < self::MIN_TERM_LENGTH) {
            throw new SearchException(\sprintf('The \'%s\' value must be at least %d characters, \'%s\' given.', $name, self::MIN_TERM_LENGTH, $value));
        }

        return $value;
    }

    /**
     * @param string|array<array-key, string>|mixed $values
     *
     * @return array<int, string>
     */
    private static function assertValues($values, string $name): array
    {
        $values = \is_string($values) ? [$values] : $values;
        if (!\is_array($values) || $values === []) {
            throw new SearchException(\sprintf('The \'%s\' value must be a non-empty list of strings.', $name));
        }

        foreach ($values as $value) {
            if (!\is_string($value) || $value === '') {
                throw new SearchException(\sprintf('The \'%s\' value must be a non-empty list of strings.', $name));
            }
        }

        return \array_values($values);
    }

    private static function assertOperator(string $operator, string $name): string
    {
        if (!\in_array($operator, self::OPERATORS, true)) {
            throw new SearchException(\sprintf('Operator \'%s\' is not supported for \'%s\'. Supported operators are %s.', $operator, $name, \implode(', ', self::OPERATORS)));
        }

        return $operator;
    }
}
