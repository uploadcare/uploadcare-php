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

    /**
     * @param string      $targetUrl     Webhook endpoint
     * @param bool        $isActive      Is webhook active
     * @param string|null $signingSecret Secret for sign a webhook endpoint call
     * @param string      $event         The event can be any of `file.uploaded`, `file.info_updated`, `file.deleted`, `file.stored`, `file.infected`
     *
     * @return WebhookInterface Result of creation
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#tag/Webhook
     * @see https://uploadcare.com/docs/webhooks/
     */
    public function createWebhook(string $targetUrl, bool $isActive = true, ?string $signingSecret = null, string $event = 'file.uploaded'): WebhookInterface;

    /**
     * @param array $parameters Parameters for update: string `target_url`, bool `is_active`
     */
    public function updateWebhook(int $id, array $parameters): WebhookInterface;

    public function deleteWebhook(string $targetUrl): bool;
}
