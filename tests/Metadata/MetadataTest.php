<?php declare(strict_types=1);

namespace Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Symfony\Component\String\ByteString;
use Uploadcare\Apis\MetadataApi;
use Uploadcare\Configuration;
use Uploadcare\Exception\MetadataException;
use Uploadcare\File\Metadata;

class MetadataTest extends TestCase
{
    public function generateKeys(): array
    {
        return [
            ['al5o_valid:string.with.dot', true],
            ['string-with-hypern', true],
            ['string', true],
            ['not`valid|string', false],
            [58, false],
            [(object) ['foo' => 'bar'], false],
            ['string-length-more-than-sixty-for-characters-does-not-allowed-as-well', false],
        ];
    }

    /**
     * @dataProvider generateKeys
     */
    public function testValidation($key, bool $result): void
    {
        $validationResult = Metadata::validateKey($key);
        self::assertSame($validationResult, $result);
    }

    public function testSetKeyMethodWithWrongKey(): void
    {
        $key = 'not|valid|string';
        $value = ByteString::fromRandom(64)->toString();

        $api = new MetadataApi(Configuration::create('demo', 'demo'));
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessageMatches('/Key should be string up to 64 characters length/');

        $api->setKey(\uuid_create(), $key, $value);
    }

    public function testSetKeyMethodWithWrongValue(): void
    {
        $key = 'validString';
        $value = ByteString::fromRandom(1024)->toString();
        $api = new MetadataApi(Configuration::create('demo', 'demo'));
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessageMatches('/Up to 512 characters value allowed/');

        $api->setKey(\uuid_create(), $key, $value);
    }
}
