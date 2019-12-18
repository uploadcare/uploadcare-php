<?php

namespace Uploadcare\Signature;

/**
 * Interface SignatureInterface
 * @package Uploadcare\SecureSignature
 */
interface SignatureInterface
{
    /**
     * The signature is an MD5 hex-encoded hash from a concatenation
     * of your `secret_key` and `expire`.
     *
     * @return string
     */
    public function getSignature();

    /**
     * Expire sets the time until your signature is valid.
     * It is a Unix timestamp.
     *
     * @return int
     */
    public function getExpire();
}
