<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\AuthUrl;

use Uploadcare\AuthUrl\Token\TokenInterface;

interface AuthUrlConfigInterface
{
    /**
     * @return TokenInterface
     */
    public function getTokenGenerator(): TokenInterface;

    /**
     * @return string
     */
    public function getCdnUrl(): string;

    /**
     * @return string|null
     */
    public function getToken(): ?string;

    /**
     * @return int|null
     */
    public function getTimeStamp(): ?int;
}
