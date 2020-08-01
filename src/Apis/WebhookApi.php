<?php

namespace Uploadcare\Apis;

use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\WebhookCollection;
use Uploadcare\Response\WebhookResponse as Webhook;
use Uploadcare\Webhook as WebhookDecorator;
use Uploadcare\WebhookCollection as WebhookCollectionDecorator;

class WebhookApi extends AbstractApi implements WebhookApiInterface
{
    /**
     * @inheritDoc
     */
    public function listWebhooks()
    {
        $response = $this->request('GET', 'webhooks/');
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), WebhookCollection::class);
        if (!$result instanceof WebhookCollection) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return new WebhookCollectionDecorator($result, $this);
    }

    /**
     * @inheritDoc
     */
    public function createWebhook($targetUrl, $isActive = true, $event = 'file.uploaded')
    {
        $response = $this->request('POST', 'webhooks/', [
            'form_params' => [
                'target_url' => $targetUrl,
                'event' => $event,
                'is_active' => $isActive,
            ],
        ]);

        // Hack
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents());
        if (!\is_array($result) || !\array_key_exists('hook', $result)) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }
        $webhook = $this->configuration->getSerializer()
            ->deserialize($result['hook'], Webhook::class);

        if (!$webhook instanceof WebhookInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return new WebhookDecorator($webhook, $this);
    }

    /**
     * @inheritDoc
     */
    public function updateWebhook(WebhookInterface $webhook)
    {
        $uri = \sprintf('webhooks/%s/', $webhook->getId());
        $response = $this->request('PUT', $uri, [
            'form_params' => [
                'target_url' => $webhook->getTargetUrl(),
                'project' => $webhook->getProject(),
                'is_active' => $webhook->isActive(),
            ],
        ]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), Webhook::class);

        if (!$result instanceof WebhookInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return new WebhookDecorator($result, $this);
    }

    /**
     * @inheritDoc
     */
    public function deleteWebhook($targetUrl)
    {
        $this->request('DELETE', 'webhooks/unsubscribe/', [
            'form_params' => ['target_url' => $targetUrl],
        ]);
    }
}
