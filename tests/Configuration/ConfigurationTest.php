<?php declare(strict_types=1);

namespace Tests\Configuration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Uploadcare\Configuration;

class ConfigurationTest extends TestCase
{
    private string $header;
    private string $publicKey = 'demo-public-key';
    private string $privateKey = 'demo-private-key';

    protected function setUp(): void
    {
        parent::setUp();
        $this->header = \vsprintf('PHPUploadcare/%s/%s (PHP/%s)', [
            Configuration::LIBRARY_VERSION,
            $this->publicKey,
            PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
        ]);
    }

    public function generateHeaders(): array
    {
        return [
            [null],
            ['Some-existing-header'],
            [false],
            ['0'],
        ];
    }

    /**
     * @dataProvider generateHeaders
     *
     * @throws \ReflectionException
     */
    public function testGetHeadersWithClient($headerString): void
    {
        $conf = Configuration::create($this->publicKey, $this->privateKey);
        if ($headerString !== null) {
            $client = (new \ReflectionObject($conf))->getProperty('client');
            $client->setAccessible(true);
            $client->setValue($conf, new Client(['headers' => ['User-Agent' => $headerString]]));
        }

        $result = $conf->getHeaders();
        self::assertArrayHasKey('User-Agent', $result);
        self::assertSame($this->header, $result['User-Agent']);
    }

    public function testHeaderWithFramework(): void
    {
        $conf = Configuration::create($this->publicKey, $this->privateKey, ['framework' => ['Symfony', '5.1']]);
        $result = $conf->getHeaders();
        self::assertArrayHasKey('User-Agent', $result);
        self::assertStringContainsString('Symfony', $result['User-Agent']);
        self::assertStringContainsString('5.1', $result['User-Agent']);
    }
}
