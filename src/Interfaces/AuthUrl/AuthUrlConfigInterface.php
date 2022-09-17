<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\AuthUrl;

use Uploadcare\AuthUrl\Token\TokenInterface;

interface AuthUrlConfigInterface
{
    public function getTokenGenerator(): TokenInterface;

    public function getCdnUrl(): string;

    public function getToken(): ?string;

    public function getTimeStamp(): ?int;
}
