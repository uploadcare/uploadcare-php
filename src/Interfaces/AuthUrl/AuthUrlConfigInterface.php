<?php

namespace Uploadcare\Interfaces\AuthUrl;

use Uploadcare\AuthUrl\Token\TokenInterface;

interface AuthUrlConfigInterface
{
    /**
     * @return TokenInterface
     */
    public function getTokenGenerator();

    /**
     * @return string
     */
    public function getCdnUrl();

    /**
     * @return string|null
     */
    public function getToken();

    /**
     * @return int|null
     */
    public function getTimeStamp();
}
