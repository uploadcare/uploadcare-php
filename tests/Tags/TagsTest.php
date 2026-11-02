<?php declare(strict_types=1);

namespace Tests\Tags;

use PHPUnit\Framework\TestCase;
use Uploadcare\Exception\TagsException;
use Uploadcare\File\Tags;

class TagsTest extends TestCase
{
    public function provideTags(): array
    {
        return [
            ['cat', true],
            ['a.b-c_d', true],
            ['2026', true],
            ['Summer', true],
            [' cat ', true],
            [\str_repeat('a', Tags::MAX_TAG_LENGTH), true],
            ['has space', false],
            ['emoji-🐱', false],
            ['plus+sign', false],
            ['at@sign', false],
            ['', false],
            ['   ', false],
            [\str_repeat('a', Tags::MAX_TAG_LENGTH + 1), false],
            [58, false],
            [(object) ['foo' => 'bar'], false],
            [null, false],
        ];
    }

    /**
     * @dataProvider provideTags
     */
    public function testValidateTag($tag, bool $expected): void
    {
        self::assertSame($expected, Tags::validateTag($tag));
    }

    /**
     * Normalization rules as documented in https://uploadcare.com/docs/file-tags/.
     */
    public function provideNormalization(): array
    {
        return [
            'lowercased' => [['Summer'], ['summer']],
            'whitespace stripped' => [[' cat '], ['cat']],
            'duplicates removed, first wins' => [['cat', 'Cat', 'CAT'], ['cat']],
            'empty strings discarded' => [['cat', '', 'dog'], ['cat', 'dog']],
            'order preserved by first appearance' => [['z', 'a', 'm'], ['z', 'a', 'm']],
            'non-strings discarded' => [['cat', 58, null, 'dog'], ['cat', 'dog']],
            'empty list' => [[], []],
        ];
    }

    /**
     * @dataProvider provideNormalization
     */
    public function testNormalize(array $input, array $expected): void
    {
        self::assertSame($expected, Tags::normalize($input));
    }

    public function testAssertValidReturnsNormalizedTags(): void
    {
        self::assertSame(['cat', 'animal'], Tags::assertValid(['Cat', ' animal ', 'CAT']));
    }

    public function testAssertValidDiscardsEmptyStrings(): void
    {
        self::assertSame(['cat', 'dog'], Tags::assertValid(['cat', '', '  ', 'dog']));
    }

    public function testAssertValidThrowsOnInvalidTag(): void
    {
        $this->expectException(TagsException::class);
        $this->expectExceptionMessageMatches('/Tag should be a string up to 100 characters length/');

        Tags::assertValid(['cat', 'not a tag']);
    }

    public function testAssertValidThrowsOnNonStringTag(): void
    {
        $this->expectException(TagsException::class);
        $this->expectExceptionMessageMatches('/Allowed symbols are A-z, 0-9, hyphen, underscore and dot/');

        Tags::assertValid(['cat', 58]);
    }

    public function testAssertValidThrowsOnTooManyTags(): void
    {
        $tags = [];
        for ($i = 0; $i <= Tags::MAX_TAGS; ++$i) {
            $tags[] = \sprintf('tag-%d', $i);
        }
        self::assertCount(Tags::MAX_TAGS + 1, $tags);

        $this->expectException(TagsException::class);
        $this->expectExceptionMessageMatches('/Up to 50 tags per file allowed, 51 given/');

        Tags::assertValid($tags);
    }

    public function testAssertValidAllowsOverLimitListThatDeduplicatesToLimit(): void
    {
        $tags = [];
        for ($i = 0; $i < Tags::MAX_TAGS; ++$i) {
            $tags[] = \sprintf('tag-%d', $i);
        }
        $tags[] = 'TAG-0';
        self::assertCount(Tags::MAX_TAGS + 1, $tags);

        self::assertCount(Tags::MAX_TAGS, Tags::assertValid($tags));
    }

    public function testConstructorNormalizesInput(): void
    {
        $tags = new Tags([' Cat ', 'cat', 'ANIMAL', '']);

        self::assertSame(['cat', 'animal'], $tags->toArray());
        self::assertCount(2, $tags);
    }

    public function testHasIsNormalizationAware(): void
    {
        $tags = new Tags(['cat', 'animal']);

        self::assertTrue($tags->has('cat'));
        self::assertTrue($tags->has('Cat'));
        self::assertTrue($tags->has(' CAT '));
        self::assertFalse($tags->has('dog'));
    }

    public function testToStringMakesUploadApiValue(): void
    {
        self::assertSame('cat,animal', (string) (new Tags(['cat', 'animal'])));
        self::assertSame('', (string) (new Tags()));
    }

    public function testIteration(): void
    {
        $tags = new Tags(['cat', 'animal']);
        $collected = [];
        foreach ($tags as $tag) {
            $collected[] = $tag;
        }

        self::assertSame(['cat', 'animal'], $collected);
        self::assertInstanceOf(\ArrayIterator::class, $tags->getIterator());
    }

    public function testEmptyCollectionIteratesEmpty(): void
    {
        $tags = new Tags();

        self::assertCount(0, $tags);
        self::assertSame([], $tags->toArray());
        self::assertInstanceOf(\EmptyIterator::class, $tags->getIterator());
    }
}
