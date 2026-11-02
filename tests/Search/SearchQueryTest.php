<?php declare(strict_types=1);

namespace Tests\Search;

use PHPUnit\Framework\TestCase;
use Uploadcare\Exception\SearchException;
use Uploadcare\Exception\TagsException;
use Uploadcare\Search\SearchQuery;

class SearchQueryTest extends TestCase
{
    public function testEmptyQueryIsRejected(): void
    {
        $this->expectException(SearchException::class);
        $this->expectExceptionMessage('At least one search criterion must be specified.');

        (new SearchQuery())->toArray();
    }

    public function testModifiersAloneAreNotACriterion(): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->fuzziness()->sort('-size')->toArray();
    }

    public function testQuery(): void
    {
        self::assertSame(['query' => 'vacation'], (new SearchQuery())->query('vacation')->toArray());
    }

    public function provideShortTerms(): array
    {
        return [
            'three ascii characters' => ['cat'],
            'padded short term' => ['  cat  '],
            'three multibyte characters' => ['кот'],
        ];
    }

    /**
     * @dataProvider provideShortTerms
     */
    public function testShortQueryIsRejected(string $term): void
    {
        $this->expectException(SearchException::class);
        $this->expectExceptionMessage('at least 4 characters');

        (new SearchQuery())->query($term);
    }

    public function testFourMultibyteCharactersAreAccepted(): void
    {
        self::assertSame(['query' => 'кошк'], (new SearchQuery())->query('кошк')->toArray());
    }

    public function testPhrase(): void
    {
        $body = (new SearchQuery())
            ->phrase(SearchQuery::FIELD_ORIGINAL_FILENAME, 'report')
            ->phrase(SearchQuery::FIELD_MIME_TYPE, 'pdfs')
            ->toArray();

        self::assertSame(['phrase' => ['original_filename' => 'report', 'detected_mime_type' => 'pdfs']], $body);
    }

    public function testPhraseRejectsUnknownField(): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->phrase(SearchQuery::FIELD_UUID, '575ed4e8');
    }

    public function testPhraseRejectsShortValue(): void
    {
        $this->expectException(SearchException::class);
        $this->expectExceptionMessage('phrase.original_filename');

        (new SearchQuery())->phrase(SearchQuery::FIELD_ORIGINAL_FILENAME, 'abc');
    }

    public function testExact(): void
    {
        $body = (new SearchQuery())
            ->exact(SearchQuery::FIELD_ORIGINAL_FILENAME, ['logo.png', 'icon.png'])
            ->exact(SearchQuery::FIELD_MIME_TYPE, 'image/png')
            ->exactMetadata('order_id', ['12345', '12346'])
            ->toArray();

        self::assertSame(['exact' => [
            'original_filename' => ['logo.png', 'icon.png'],
            'detected_mime_type' => ['image/png'],
            'metadata[order_id]' => ['12345', '12346'],
        ]], $body);
    }

    public function testExactAcceptsShortValues(): void
    {
        $body = (new SearchQuery())->exact(SearchQuery::FIELD_ORIGINAL_FILENAME, 'a.js')->toArray();

        self::assertSame(['exact' => ['original_filename' => ['a.js']]], $body);
    }

    public function testExactRejectsMetadataField(): void
    {
        $this->expectException(SearchException::class);
        $this->expectExceptionMessage('exactMetadata()');

        (new SearchQuery())->exact(SearchQuery::FIELD_METADATA, 'Canon');
    }

    public function provideInvalidExactValues(): array
    {
        return [
            'empty list' => [[]],
            'empty string' => [''],
            'non-string value' => [[123]],
        ];
    }

    /**
     * @dataProvider provideInvalidExactValues
     */
    public function testExactRejectsInvalidValues($values): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->exact(SearchQuery::FIELD_UUID, $values);
    }

    public function testFieldCannotBeInPhraseAndExact(): void
    {
        $query = (new SearchQuery())
            ->phrase(SearchQuery::FIELD_ORIGINAL_FILENAME, 'invoice')
            ->exact(SearchQuery::FIELD_ORIGINAL_FILENAME, 'invoice.pdf');

        $this->expectException(SearchException::class);
        $this->expectExceptionMessage('Fields cannot appear in both phrase and exact: original_filename.');

        $query->toArray();
    }

    public function testRanges(): void
    {
        $body = (new SearchQuery())
            ->datetimeUploaded(SearchQuery::GTE, new \DateTimeImmutable('2024-01-01T03:00:00+03:00'))
            ->datetimeUploaded(SearchQuery::LT, new \DateTimeImmutable('2025-01-01T00:00:00Z'))
            ->size(SearchQuery::GTE, 1024)
            ->size(SearchQuery::LTE, 10485760)
            ->toArray();

        self::assertSame([
            'datetime_uploaded' => ['gte' => '2024-01-01T00:00:00Z', 'lt' => '2025-01-01T00:00:00Z'],
            'size' => ['gte' => 1024, 'lte' => 10485760],
        ], $body);
    }

    public function testRangeRejectsUnknownOperator(): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->size('eq', 10);
    }

    public function testSizeRejectsNegativeValue(): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->size(SearchQuery::GT, -1);
    }

    public function testIsImageFalseIsACriterion(): void
    {
        self::assertSame(['is_image' => false], (new SearchQuery())->isImage(false)->toArray());
    }

    public function testTagsAreNormalized(): void
    {
        $body = (new SearchQuery())
            ->tagsAny(['Cat', ' dog ', 'cat'])
            ->tagsAll(['outdoor'])
            ->tagsNone(['archived'])
            ->toArray();

        self::assertSame(['tags' => ['any' => ['cat', 'dog'], 'all' => ['outdoor'], 'none' => ['archived']]], $body);
    }

    public function testEmptyTagListIsNotACriterion(): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->tagsAny(['  '])->toArray();
    }

    public function testInvalidTagIsRejected(): void
    {
        $this->expectException(TagsException::class);

        (new SearchQuery())->tagsAll(['two words']);
    }

    public function testModifiers(): void
    {
        $body = (new SearchQuery())
            ->query('barbarain')
            ->fuzziness()
            ->sort('-datetime_uploaded', 'original_filename')
            ->toArray();

        self::assertSame([
            'query' => 'barbarain',
            'fuzziness' => true,
            'sort' => ['-datetime_uploaded', 'original_filename'],
        ], $body);
    }

    public function provideInvalidSort(): array
    {
        return [
            'no keys' => [[]],
            'too many keys' => [['score', 'size', 'datetime_uploaded', 'original_filename', '-score']],
            'unknown key' => [['uuid']],
            'double prefix' => [['--size']],
            'duplicate key' => [['size', 'size']],
            'both directions' => [['size', '-size']],
        ];
    }

    /**
     * @dataProvider provideInvalidSort
     */
    public function testInvalidSortIsRejected(array $keys): void
    {
        $this->expectException(SearchException::class);

        (new SearchQuery())->sort(...$keys);
    }

    public function testJsonSerialization(): void
    {
        $query = (new SearchQuery())->isImage()->sort('-size');

        self::assertSame('{"is_image":true,"sort":["-size"]}', \json_encode($query));
    }
}
