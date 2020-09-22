<?php

namespace Uploadcare\AuthUrl;

use Uploadcare\AuthUrl\Token\TokenInterface;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;

/**
 * Class AuthUrlConfig.
 */
class AuthUrlConfig implements AuthUrlConfigInterface
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var string
     */
    private $cdnUrl;

    /**
     * AuthUrlConfig constructor.
     *
     * @param string         $cdnUrl
     * @param TokenInterface $token
     */
    public function __construct($cdnUrl, TokenInterface $token)
    {
        $this->cdnUrl = $cdnUrl;
        $this->token = $token;
    }

    /**
     * @return TokenInterface
     */
    public function getTokenGenerator()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        return $this->token->getToken();
    }

    /**
     * @inheritDoc
     */
    public function getTimeStamp()
    {
        return $this->token->getExpired();
    }

    /**
     * @return string
     */
    public function getCdnUrl()
    {
        return $this->cdnUrl;
    }
}
