<?php

namespace Uploadcare\AuthUrl;

use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;

/**
 * Class AuthUrlConfig.
 */
class AuthUrlConfig implements AuthUrlConfigInterface
{
    /**
     * @var string|null
     */
    private $token;

    /**
     * @var int|null
     */
    private $timestamp;

    /**
     * @var string
     */
    private $cdnUrl;

    /**
     * AuthUrlConfig constructor.
     *
     * @param string               $cdnUrl
     * @param string|callable|null $token
     * @param int|callable|null    $timestamp
     * @param array                $arguments
     */
    public function __construct($cdnUrl, $token = null, $timestamp = null, $arguments = [])
    {
        $this->cdnUrl = $cdnUrl;
        if ($token !== null) {
            $this->setToken($token, $arguments);
        }

        if ($timestamp !== null) {
            $this->setTimeStamp($timestamp, $arguments);
        }
    }

    /**
     * @param string|callable $token
     * @param array           $arguments
     *
     * @return $this
     */
    public function setToken($token, $arguments = [])
    {
        if (\is_callable($token)) {
            $this->token = \call_user_func_array($token, $arguments);
        } else {
            $this->token = $token;
        }

        return $this;
    }

    /**
     * @param int|callable $timestamp
     * @param array        $arguments
     *
     * @return $this
     */
    public function setTimeStamp($timestamp, $arguments = [])
    {
        if (\is_callable($timestamp)) {
            $this->timestamp = \call_user_func_array($timestamp, $arguments);
        } else {
            $this->timestamp = $timestamp;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function getTimeStamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getCdnUrl()
    {
        return $this->cdnUrl;
    }
}
