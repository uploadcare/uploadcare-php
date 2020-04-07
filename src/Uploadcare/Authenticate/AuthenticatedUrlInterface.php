<?php

namespace Uploadcare\Authenticate;

/**
 * Interface AuthenticatedUrlInterface
 * @package Authenticate\AuthenticatedUrl
 */
interface AuthenticatedUrlInterface
{
    /**
     * The signature is an MD5 hex-encoded hash from a concatenation
     * of your `secret_key` and `expire`.
     *
     * @param $url string
     * @return string
     */
    public function getAuthenticatedUrl($url);
}
