<?php declare(strict_types=1);

namespace Tests\Metadata;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\String\ByteString;
use Uploadcare\Apis\MetadataApi;
use Uploadcare\Configuration;
use Uploadcare\Exception\{HttpException, MetadataException};
use Uploadcare\File\Metadata;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\SerializerFactory;

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

    public function testRemoveReyWithTheWrongKey(): void
    {
        $key = ByteString::fromRandom(128)->toString();

        $api = new MetadataApi(Configuration::create('demo', 'demo'));
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessageMatches('/Key should be string up to 64 characters length/');

        $api->removeKey(\uuid_create(), $key);
    }

    public function testRemoveKeyWithAnErrorInTheResponse(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::atLeastOnce())->method('request')->willThrowException(new ConnectException('Error string', new Request('DELETE', 'https://example.com')));
        $configuration = new Configuration('demo', new Signature('demo'), $client, SerializerFactory::create());

        $key = 'validString';
        $api = new MetadataApi($configuration);
        $this->expectException(HttpException::class);

        $api->removeKey(\uuid_create(), $key);
    }

    public function testRemoveKeyWithTheWrongResponseStatus(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::atLeastOnce())->method('getStatusCode')->willReturn(401);
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::atLeastOnce())->method('request')->willReturn($response);
        $configuration = new Configuration('demo', new Signature('demo'), $client, SerializerFactory::create());

        $key = 'validString';
        $api = new MetadataApi($configuration);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessageMatches('/Wrong response. Call to support/');

        $api->removeKey(\uuid_create(), $key);
    }
}
