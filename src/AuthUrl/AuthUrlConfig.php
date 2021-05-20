<?php declare(strict_types=1);

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
    public function __construct(string $cdnUrl, TokenInterface $token)
    {
        $this->cdnUrl = $cdnUrl;
        $this->token = $token;
    }

    public function getTokenGenerator(): TokenInterface
    {
        return $this->token;
    }

    public function getToken(): string
    {
        return $this->token->getToken();
    }

    public function getTimeStamp(): int
    {
        return $this->token->getExpired();
    }

    public function getCdnUrl(): string
    {
        return $this->cdnUrl;
    }
}
