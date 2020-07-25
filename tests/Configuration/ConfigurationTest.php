<?php


namespace Tests\Configuration;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Uploadcare\Configuration;

class ConfigurationTest extends TestCase
{
    private $header;
    private $publicKey = 'demo-public-key';
    private $privateKey = 'demo-private-key';

    protected function setUp()
    {
        parent::setUp();
        $this->header = \vsprintf('PHPUploadcare/%s/%s (PHP/%s)', [
            Configuration::LIBRARY_VERSION,
            $this->publicKey,
            PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
        ]);
    }

    public function generateHeaders()
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
     * @param $headerString
     * @throws \ReflectionException
     */
    public function testUserAgentStringCreation($headerString)
    {
        $conf = Configuration::create($this->publicKey, $this->privateKey);

        $headers = [];
        if ($headerString !== null) {
            $headers['User-Agent'] = $headerString;
        }

        $setUserAgent = (new ReflectionObject($conf))->getMethod('setUserAgent');
        $setUserAgent->setAccessible(true);
        $setUserAgent->invokeArgs($conf, [&$headers]);

        self::assertNotEmpty($headers);
        self::assertArrayHasKey('User-Agent', $headers);
        self::assertSame($this->header, $headers['User-Agent']);
    }

    /**
     * @dataProvider generateHeaders
     * @param $headerString
     * @throws \ReflectionException
     */
    public function testGetHeadersWithClient($headerString)
    {
        $conf = Configuration::create($this->publicKey, $this->privateKey);
        if ($headerString !== null) {
            $client = (new ReflectionObject($conf))->getProperty('client');
            $client->setAccessible(true);
            $client->setValue($conf, new Client(['headers' => ['User-Agent' => $headerString]]));
        }

        $result = $conf->getHeaders();
        self::assertArrayHasKey('User-Agent', $result);
        self::assertSame($this->header, $result['User-Agent']);
    }
}
