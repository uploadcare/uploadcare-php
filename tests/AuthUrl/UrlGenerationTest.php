<?php

namespace Tests\AuthUrl;

use PHPUnit\Framework\TestCase;
use Uploadcare\AuthUrl\AkamaiUrlGenerator;
use Uploadcare\AuthUrl\AuthUrlConfig;
use Uploadcare\AuthUrl\KeyCdnUrlGenerator;
use Uploadcare\AuthUrl\Token\TokenInterface;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;

/**
 * Class UrlGenerationTest
 */
class UrlGenerationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    protected function setUp()
    {
        parent::setUp();
        $authUrlConfig = new AuthUrlConfig('host.domain.com', $this->createMock(TokenInterface::class));
        $this->config = Configuration::create('public-key', 'private-key');
        $this->config->setAuthUrlConfig($authUrlConfig);
    }

    public function provideClasses()
    {
        return [
            [new AkamaiUrlGenerator()],
            [new KeyCdnUrlGenerator()],
        ];
    }

    /**
     * @dataProvider provideClasses
     *
     * @param $generator
     *
     * @throws \ReflectionException
     */
    public function testAkamaiUrlGenerator($generator)
    {
        self::markTestSkipped('Change logic');

        $ac = $this->config->getAuthUrlConfig();
        self::assertInstanceOf(AuthUrlConfigInterface::class, $ac);

        $uuid = \uuid_create();
        $value = $generator->getUrl($ac, $uuid);

        self::assertNotEmpty($value);
        self::assertNotEmpty($ac->getToken());
        self::assertNotEmpty($ac->getTimeStamp());

        $template = (new \ReflectionObject($generator))->getProperty('template');
        $template->setAccessible(true);
        $templateValue = $template->getValue($generator);

        $check = \strtr($templateValue, [
            '{cdn}' => $ac->getCdnUrl(),
            '{uuid}' => $uuid,
            '{timestamp}' => $ac->getTimeStamp(),
            '{token}' => $ac->getToken(),
        ]);

        self::assertEquals($check, $value);
    }
}
