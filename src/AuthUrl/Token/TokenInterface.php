<?php declare(strict_types=1);

namespace Uploadcare\AuthUrl\Token;

/**
 * Interface TokenInterface.
 * Generate tokens for CDNs.
 */
interface TokenInterface
{
    /**
     * Token string.
     */
    public function getToken(): string;

    /**
     * Token expiration timestamp.
     */
    public function getExpired(): int;

    /**
     * URL template for CDN.
     */
    public function getUrlTemplate(): string;
}
