<?php

error_reporting(E_ALL);
require_once __DIR__ . './../../config.php';
require_once __DIR__ . './../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Uploadcare\Authenticate\AkamaiAuthenticatedUrl;

class AkamaiAuthenticatedUrlTest extends TestCase
{
    public function AuthenticatedUrlDataProvider()
    {
        return array(
            array(
                UC_SECRET_KEY, 30 * 60,
            ),
        );
    }

    /**
     * @dataProvider AuthenticatedUrlDataProvider
     * @param string $secretKey
     * @param int $expireTimeInSeconds
     * @throws Exception
     */
    public function test__construct($secretKey, $expireTimeInSeconds)
    {
        $authenticatedUrl = new AkamaiAuthenticatedUrl($secretKey, $expireTimeInSeconds);

        $this->assertTrue(in_array("Uploadcare\Authenticate\AuthenticatedUrlInterface", class_implements($authenticatedUrl)));
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Uploadcare\Authenticate\AkamaiAuthenticatedUrl');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @dataProvider AuthenticatedUrlDataProvider
     * @param string $secretKey
     * @param int $expireTimeInSeconds
     * @throws Exception
     */
    public function test_h2b($secretKey, $expireTimeInSeconds)
    {
        $authenticatedUrl = new AkamaiAuthenticatedUrl($secretKey, $expireTimeInSeconds);

        $h2b = self::getMethod('h2b');
        $result = $h2b->invokeArgs($authenticatedUrl, array('414243'));

        $this->assertEquals($result, 'ABC');
    }

    /**
     * @dataProvider AuthenticatedUrlDataProvider
     * @param string $secretKey
     * @param int $expireTimeInSeconds
     * @throws Exception
     */
    public function testgetExprField($secretKey, $expireTimeInSeconds)
    {
        $authenticatedUrl = new AkamaiAuthenticatedUrl($secretKey, $expireTimeInSeconds);

        $dateTime = new DateTime();
        $dateTimeTimestamp = $dateTime->getTimestamp();
        $expireTimestamp = $dateTimeTimestamp + $expireTimeInSeconds;

        $getExprField = self::getMethod('getExprField');
        $getFieldDelimiter = self::getMethod('getFieldDelimiter');

        $this->assertEquals('exp=' . $expireTimestamp . $getFieldDelimiter->invoke($authenticatedUrl), $getExprField->invoke($authenticatedUrl));
    }
}
