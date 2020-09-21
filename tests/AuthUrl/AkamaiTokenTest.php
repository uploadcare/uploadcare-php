<?php

namespace Tests\AuthUrl;

use PHPUnit\Framework\TestCase;
use Uploadcare\AuthUrl\Token\AkamaiToken;
use Uploadcare\AuthUrl\Token\TokenException;

class AkamaiTokenTest extends TestCase
{
    private $key;

    protected function setUp()
    {
        parent::setUp();
        $this->key = \bin2hex(\random_bytes(32));
    }

    public function testEmptyTokenCreation()
    {
        $token = new AkamaiToken($this->key);

        self::assertEquals($this->key, $token->getKey());
        self::assertNotEmpty($token->getToken());
        self::assertNotEmpty($token->getExpired());
    }

    public function testSetWrongKey()
    {
        $this->expectException(TokenException::class);
        new AkamaiToken('00~00');
        $this->expectExceptionMessageRegExp('Key must be a hex string');
    }

    public function testSetWrongWindow()
    {
        $this->expectException(TokenException::class);
        new AkamaiToken($this->key, 'not-a-number');
        $this->expectExceptionMessageRegExp('Window must me a number');
    }

    public function testSetInvalidAlgorithm()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->setAlgo('UNKNOWN');
        $this->expectExceptionMessageRegExp('Invalid algorithm');
    }

    public function testSetValidAlgorithm()
    {
        $token = new AkamaiToken($this->key);
        $token->setAlgo('sha256');
        self::assertEquals('sha256', $token->getAlgo());
    }

    public function testSetValidIp()
    {
        $token = new AkamaiToken($this->key);
        $token->setIp('5.26.144.17');
        self::assertEquals('5.26.144.17', $token->getIp());
        $token->setIp('fd00::54:20c:29fe:fe14:ab4b');
        self::assertEquals('fd00::54:20c:29fe:fe14:ab4b', $token->getIp());
    }

    public function testSetIpIsNull()
    {
        $token = new AkamaiToken($this->key);
        $token->setIp(null);
        self::assertNull($token->getIp());
    }

    public function testSetInvalidIp()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->setIp('964.254.96.22');
        $this->expectExceptionMessageRegExp('neither IPv4, nor IPv6');
    }

    public function testSetNotAnIp()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->setIp(9642549622);
        $this->expectExceptionMessageRegExp('IP must be a string');
    }

    public function testSetInvalidStartTime()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->setStartTime('not valid');
        $token->getStartTime();
        $this->expectExceptionMessageRegExp('Start time input invalid or out of range');
    }

    public function testSetValidStartTime()
    {
        $time = \date_create()->getTimestamp();
        $token = new AkamaiToken($this->key);
        $token->setStartTime($time);
        self::assertEquals($time, $token->getStartTime());
    }

    public function testSetAclWithUrl()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->setAcl('ACL');
        $token->setUrl('http://localhost');
        $this->expectExceptionMessageRegExp('Cannot set both an');
    }

    public function testSetUrlWithAcl()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->setUrl('http://localhost');
        $token->setAcl('ACL');
        $this->expectExceptionMessageRegExp('Cannot set both an');
    }

    public function testSetSessionId()
    {
        $token = new AkamaiToken($this->key);
        $token->setSessionId('PHP_SESSID_0092FD');
        self::assertEquals('PHP_SESSID_0092FD', $token->getSessionId());
    }

    public function testSetData()
    {
        $token = new AkamaiToken($this->key);
        $token->setData('some-data');
        self::assertEquals('some-data', $token->getData());
    }

    public function testSetDelimiter()
    {
        $token = new AkamaiToken($this->key);
        $token->setFieldDelimiter('^');
        self::assertEquals('^', $token->getFieldDelimiter());
    }

    public function testSetSalt()
    {
        $token = new AkamaiToken($this->key);
        $token->setSalt('SALT');
        self::assertEquals('SALT', $token->getSalt());
    }
}
