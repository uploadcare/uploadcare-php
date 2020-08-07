<?php

namespace Tests\AuthUrl;

use PHPUnit\Framework\TestCase;
use Uploadcare\AuthUrl\AuthUrlConfig;
use Uploadcare\Configuration;

class UrlGeneratorConfigTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    protected function setUp()
    {
        parent::setUp();
        $this->config = Configuration::create('public-key', 'private-key');
    }

    public function testAuthConfigIsNull()
    {
        self::assertNull($this->config->getAuthUrlConfig());
    }

    public function testEmptyAuthConfigCreation()
    {
        $config = new AuthUrlConfig('host.domain.com');
        self::assertNull($config->getToken());
        self::assertNull($config->getTimeStamp());
        self::assertEquals('host.domain.com', $config->getCdnUrl());
    }

    public function testStringAuthConfigCreation()
    {
        $ts = \date_create()->getTimestamp();

        $config = new AuthUrlConfig('host.domain.com', 'some-token', $ts);
        self::assertEquals($ts, $config->getTimeStamp());
        self::assertEquals('some-token', $config->getToken());
        self::assertEquals('host.domain.com', $config->getCdnUrl());
    }

    public function testCallableAuthConfigCreation()
    {
        $ts = static function () {
            return \date_create()->getTimestamp();
        };
        $token = [$this, 'getToken'];

        $validTs = $ts();
        $validToken = $token();

        $config = new AuthUrlConfig('host.domain.com', $token, $ts);
        self::assertEquals($validTs, $config->getTimeStamp());
        self::assertEquals($validToken, $config->getToken());
    }

    public function getToken()
    {
        return 'some-token';
    }
}
