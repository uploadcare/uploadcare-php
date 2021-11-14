<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

interface WebhookInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return \DateTimeInterface
     */
    public function getCreated(): \DateTimeInterface;

    /**
     * @return \DateTimeInterface
     */
    public function getUpdated(): \DateTimeInterface;

    /**
     * @return string
     */
    public function getEvent(): string;

    /**
     * @return string
     */
    public function getTargetUrl(): string;

    /**
     * @return int
     */
    public function getProject(): int;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @return string|null
     */
    public function getSigningSecret(): ?string;
}
