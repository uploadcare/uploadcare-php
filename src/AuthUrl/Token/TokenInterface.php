<?php

namespace Uploadcare\AuthUrl\Token;

/**
 * Interface TokenInterface.
 * Generate tokens for CDNs.
 */
interface TokenInterface
{
    /**
     * Token string.
     *
     * @return string
     */
    public function getToken();

    /**
     * Token expiration timestamp.
     *
     * @return int
     */
    public function getExpired();
}
