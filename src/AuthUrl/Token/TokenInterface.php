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
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Token expiration timestamp.
     *
     * @return int
     */
    public function getExpired(): int;

    /**
     * URL template for CDN.
     *
     * @return string
     */
    public function getUrlTemplate(): string;
}
