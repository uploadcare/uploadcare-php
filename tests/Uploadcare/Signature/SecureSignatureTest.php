<?php

error_reporting(E_ALL);
require_once __DIR__ . './../../config.php';
require_once __DIR__ . './../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Uploadcare\Signature\SecureSignature;

class SecureSignatureTest extends TestCase
{
    public function signatureDataProvider()
    {
        return array(
            array(
                UC_SECRET_KEY, 30 * 60,
            ),
        );
    }

    /**
     * @dataProvider signatureDataProvider
     * @param string $secretKey
     * @param int $expireTimeInSeconds
     * @throws Exception
     */
    public function test__construct($secretKey, $expireTimeInSeconds)
    {
        $secureSignature = new SecureSignature($secretKey, $expireTimeInSeconds);

        $this->assertTrue(in_array("Uploadcare\Signature\SignatureInterface", class_implements($secureSignature)));
    }

    /**
     * @dataProvider signatureDataProvider
     * @param string $secretKey
     * @param int $expireTimeInSeconds
     * @throws Exception
     */
    public function testGetSignature($secretKey, $expireTimeInSeconds)
    {
        $secureSignature = new SecureSignature($secretKey, $expireTimeInSeconds);

        $dateTime = new DateTime();
        $dateTimeTimestamp = $dateTime->getTimestamp();
        $expireTimestamp = $dateTimeTimestamp + $expireTimeInSeconds;

        $toSign = $secretKey . $expireTimestamp;

        $this->assertEquals(md5($toSign), $secureSignature->getSignature());
    }

    /**
     * @dataProvider signatureDataProvider
     * @param string $secretKey
     * @param int $expireTimeInSeconds
     * @throws Exception
     */
    public function testGetExpire($secretKey, $expireTimeInSeconds)
    {
        $secureSignature = new SecureSignature($secretKey, $expireTimeInSeconds);

        $dateTime = new DateTime();
        $dateTimeTimestamp = $dateTime->getTimestamp();
        $expireTimestamp = $dateTimeTimestamp + $expireTimeInSeconds;

        $this->assertEquals($expireTimestamp, $secureSignature->getExpire());
    }
}
