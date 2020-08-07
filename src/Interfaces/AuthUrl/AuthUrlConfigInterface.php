<?php

namespace Uploadcare\Interfaces\AuthUrl;

interface AuthUrlConfigInterface
{
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
