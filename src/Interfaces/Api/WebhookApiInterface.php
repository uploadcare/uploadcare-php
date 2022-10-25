<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;

/**
 * Webhooks management.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#tag/Webhook
 */
interface WebhookApiInterface
{
    /**
     * @return CollectionInterface<int, WebhookInterface>
     */
    public function listWebhooks(): CollectionInterface;

    public function createWebhook(string $targetUrl, bool $isActive = true, string $signingSecret = null, string $event = 'file.uploaded'): WebhookInterface;

    /**
     * @param array $parameters Parameters for update: string `target_url`, bool `is_active`
     */
    public function updateWebhook(int $id, array $parameters): WebhookInterface;

    public function deleteWebhook(string $targetUrl): bool;
}
