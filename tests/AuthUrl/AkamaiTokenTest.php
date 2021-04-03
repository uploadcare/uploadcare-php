<?php

namespace Tests\AuthUrl;

use PHPUnit\Framework\TestCase;
use Uploadcare\AuthUrl\Token\AkamaiToken;
use Uploadcare\AuthUrl\Token\TokenException;

class AkamaiTokenTest extends TestCase
{
    private $key;

    protected function setUp(): void
    {
        parent::setUp();
        $this->key = \bin2hex(\random_bytes(32));
    }

    public function testEmptyTokenCreation()
    {
        $token = new AkamaiToken($this->key);
        $token->setAcl(\uuid_create());

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

    public function testNoAclInObject()
    {
        $this->expectException(TokenException::class);
        $token = new AkamaiToken($this->key);
        $token->getAcl();
        $this->expectExceptionMessageRegExp('You must set file uuid as ACL');
    }

    public function testSetValidAlgorithm()
    {
        $token = new AkamaiToken($this->key);
        $token->setAlgo('sha256');
        self::assertEquals('sha256', $token->getAlgo());
    }
}
