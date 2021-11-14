<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\WebhookResponse;

final class Webhook implements WebhookInterface
{
    /**
     * @var WebhookResponse|WebhookInterface
     */
    private $inner;

    /**
     * @var WebhookApiInterface
     */
    private $api;

    public function __construct(WebhookInterface $inner, WebhookApiInterface $api)
    {
        $this->inner = $inner;
        $this->api = $api;
    }

    public function delete(): bool
    {
        return $this->api->deleteWebhook($this->getTargetUrl());
    }

    public function updateUrl(string $url): WebhookInterface
    {
        return $this->api->updateWebhook($this->getId(), ['target_url' => $url]);
    }

    public function activate(): WebhookInterface
    {
        return $this->api->updateWebhook($this->getId(), ['is_active' => true]);
    }

    public function deactivate(): WebhookInterface
    {
        return $this->api->updateWebhook($this->getId(), ['is_active' => false]);
    }

    public function getId(): int
    {
        return $this->inner->getId();
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->inner->getCreated();
    }

    public function getUpdated(): \DateTimeInterface
    {
        return $this->inner->getUpdated();
    }

    public function getEvent(): string
    {
        return $this->inner->getEvent();
    }

    public function getTargetUrl(): string
    {
        return $this->inner->getTargetUrl();
    }

    public function getProject(): int
    {
        return $this->inner->getProject();
    }

    public function isActive(): bool
    {
        return $this->inner->isActive();
    }

    public function getSigningSecret(): ?string
    {
        return $this->inner->getSigningSecret();
    }
}
