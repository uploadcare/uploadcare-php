<?php

namespace Uploadcare\Interfaces;

/**
 * Signature for upload requests.
 */
interface SignatureInterface
{
    const ALGORITHM = 'sha256';
    const MAX_TTL = 3600;

    /**
     * @return string
     */
    public function getSignature();

    /**
     * @return \DateTimeInterface
     */
    public function getExpire();
}
