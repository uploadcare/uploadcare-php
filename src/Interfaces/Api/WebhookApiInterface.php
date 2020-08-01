<?php

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;

/**
 * Webhooks management.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.5.0/#tag/Webhook
 */
interface WebhookApiInterface
{
    /**
     * @return CollectionInterface<array-key, WebhookInterface>
     */
    public function listWebhooks();

    /**
     * @param string $targetUrl
     * @param bool   $isActive
     * @param string $event
     *
     * @return WebhookInterface
     */
    public function createWebhook($targetUrl, $isActive = true, $event = 'file.uploaded');

    /**
     * @param WebhookInterface $webhook
     *
     * @return WebhookInterface
     */
    public function updateWebhook(WebhookInterface $webhook);

    /**
     * @param string $targetUrl
     *
     * @return void
     */
    public function deleteWebhook($targetUrl);
}
