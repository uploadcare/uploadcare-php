<?php

namespace Uploadcare\Security;

use Uploadcare\Interfaces\SignatureInterface;
use Uploadcare\Interfaces\UploadcareAuthInterface;

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
        $signString = $this->getExpire()->getTimestamp();

        return \hash_hmac(SignatureInterface::SIGN_ALGORITHM, (string) $signString, $this->privateKey);
    }

    /**
     * @inheritDoc
     */
    public function getExpire()
    {
        return $this->expired;
    }

    /**
     * @inheritDoc
     */
    public function getDateHeaderString($date = null)
    {
        $now = new \DateTime();
        if ($date instanceof \DateTimeInterface) {
            $now->setTimestamp($date->getTimestamp());
        }
        $now->setTimezone(new \DateTimeZone('GMT'));

        return $now->format(UploadcareAuthInterface::HEADER_DATE_FORMAT);
    }

    /**
     * @inheritDoc
     */
    public function getAuthHeaderString($method, $uri, $data, $contentType = 'application/json', $date = null)
    {
        $uri = \sprintf('/%s', \ltrim($uri, '/'));
        $data = \md5($data);
        $dateString = $this->getDateHeaderString($date);

        $signString = \implode("\n", [
            $method,
            $data,
            $contentType,
            $dateString,
            $uri,
        ]);

        return \hash_hmac(UploadcareAuthInterface::AUTH_ALGORITHM, $signString, $this->privateKey);
    }
}
