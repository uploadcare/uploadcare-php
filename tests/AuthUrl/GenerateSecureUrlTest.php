<?php declare(strict_types=1);

namespace Tests\AuthUrl;

use PHPUnit\Framework\TestCase;
use Uploadcare\Api;
use Uploadcare\Apis\FileApi;
use Uploadcare\AuthUrl\AuthUrlConfig;
use Uploadcare\AuthUrl\Token\AkamaiToken;
use Uploadcare\Configuration;
use Uploadcare\Exception\InvalidArgumentException;

class GenerateSecureUrlTest extends TestCase
{
    /**
     * @var \Uploadcare\Interfaces\Api\FileApiInterface
     */
    private $fileApi;

    protected function setUp()
    {
        parent::setUp();
        $key = \bin2hex(\random_bytes(32));

        $authUrlConfig = new AuthUrlConfig('mydomain.com', new AkamaiToken($key, 300));
        $this->fileApi = new FileApi(Configuration::create('demopublickey', 'demosecretkey')->setAuthUrlConfig($authUrlConfig));
    }

    public function testWrongObjectInMethod(): void
    {
        $object = (object) ['foo' => 'bar'];
        $this->expectException(InvalidArgumentException::class);
        $this->fileApi->generateSecureUrl($object);
    }

    public function testWrongTransformationUrl(): void
    {
        $url = '/foo/-bar/baz';
        $this->expectException(InvalidArgumentException::class);
        $this->fileApi->generateSecureUrl($url);
    }

    public function provideValidUrls(): array
    {
        return [
            ['/1a86c6b8-4e77-44c2-9da7-4228ad9a2dd8/-/format/auto/-/quality/smart/-/resize/950x/result.jpg'],
            ['/*/']
        ];
    }

    /**
     * @dataProvider provideValidUrls
     */
    public function testValidUrls(string $url): void
    {
        self::assertIsString($this->fileApi->generateSecureUrl($url));
    }
}
