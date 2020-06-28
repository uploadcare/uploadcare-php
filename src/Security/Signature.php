<?php

namespace Uploadcare\Security;

use Uploadcare\Interfaces\SignatureInterface;

class Signature implements SignatureInterface
{
    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var \DateTimeInterface
     */
    private $expired;

    /**
     * @param string   $privateKey Uploadcare private key
     * @param int|null $ttl        Signature time-to-life
     */
    public function __construct($privateKey, $ttl = null)
    {
        $this->privateKey = $privateKey;
        if ($ttl === null || $ttl > self::MAX_TTL) {
            $ttl = self::MAX_TTL;
        }
        $ts = \date_create()->getTimestamp() + $ttl;

        $this->expired = \date_create()->setTimestamp($ts);
    }

    /**
     * @inheritDoc
     */
    public function getSignature()
    {
        $signString = $this->privateKey . $this->getExpire()->getTimestamp();

        return \hash_hmac(self::ALGORITHM, $signString, $this->privateKey);
    }

    /**
     * @inheritDoc
     */
    public function getExpire()
    {
        return $this->expired;
    }
}
