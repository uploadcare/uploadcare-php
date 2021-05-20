<?php

namespace Tests\AuthUrl;

use PHPUnit\Framework\TestCase;
use Uploadcare\AuthUrl\AuthUrlConfig;
use Uploadcare\AuthUrl\Token\TokenInterface;
use Uploadcare\Configuration;

class UrlGeneratorConfigTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    protected function setUp(): void
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
        $config = new AuthUrlConfig('host.domain.com', $this->createMock(TokenInterface::class));
        self::assertEmpty($config->getToken());
        self::assertEquals(0, $config->getTimeStamp());
        self::assertEquals('host.domain.com', $config->getCdnUrl());
    }

    public function testStringAuthConfigCreation()
    {
        $ts = \date_create()->getTimestamp();
        /** @var \PHPUnit_Framework_MockObject_MockObject|TokenInterface $token */
        $token = $this->getMockBuilder(TokenInterface::class)
            ->setMethods(['getToken', 'getExpired', 'getUrlTemplate'])
            ->getMock()
        ;
        $token->method('getToken')
            ->willReturn('some-token');
        $token->method('getExpired')
            ->willReturn($ts);

        $config = new AuthUrlConfig('host.domain.com', $token);
        self::assertEquals($ts, $config->getTimeStamp());
        self::assertEquals('some-token', $config->getToken());
        self::assertEquals('host.domain.com', $config->getCdnUrl());
    }
}
