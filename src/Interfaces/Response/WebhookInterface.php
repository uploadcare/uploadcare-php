<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

interface WebhookInterface
{
    public function getId(): int;

    public function getCreated(): ?\DateTimeInterface;

    public function getUpdated(): ?\DateTimeInterface;

    public function getEvent(): ?string;

    public function getTargetUrl(): ?string;

    public function getProject(): int;

    public function isActive(): bool;

    public function getSigningSecret(): ?string;
}
