<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\WebhookCollection;
use Uploadcare\Response\WebhookResponse as Webhook;
use Uploadcare\Webhook as WebhookDecorator;
use Uploadcare\WebhookCollection as WebhookCollectionDecorator;

final class WebhookApi extends AbstractApi implements WebhookApiInterface
{
    /**
     * {@inheritDoc}
     */
    public function listWebhooks(): CollectionInterface
    {
        $response = $this->request('GET', 'webhooks/');
        $webhooks = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents());

        $result = new WebhookCollection();
        foreach ($webhooks as $webhook) {
            $obj = $this->configuration->getSerializer()
                ->deserialize(\json_encode($webhook), Webhook::class);

            if ($obj instanceof Webhook) {
                $result->add($obj);
            }
        }

        return new WebhookCollectionDecorator($result, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function createWebhook(string $targetUrl, bool $isActive = true, string $signingSecret = null, string $event = 'file.uploaded'): WebhookInterface
    {
        if ($signingSecret !== null) {
            $signingSecret = \substr($signingSecret, 0, 32);
        }

        $response = $this->request('POST', 'webhooks/', [
            'body' => \json_encode([
                'target_url' => $targetUrl,
                'event' => $event,
                'is_active' => $isActive,
                'signing_secret' => $signingSecret,
            ]),
        ]);

        $webhook = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), Webhook::class);

        if (!$webhook instanceof WebhookInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return new WebhookDecorator($webhook, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function updateWebhook(int $id, array $parameters): WebhookInterface
    {
        $uri = \sprintf('webhooks/%s/', $id);
        $data = [];
        if (isset($parameters['target_url'])) {
            $data['target_url'] = (string) $parameters['target_url'];
        }
        if (isset($parameters['event'])) {
            $data['event'] = (string) $parameters['event'];
        }
        if (isset($parameters['is_active'])) {
            $data['is_active'] = (bool) $parameters['is_active'];
        }
        if (\array_key_exists('signing_secret', $parameters)) {
            $data['signing_secret'] = $parameters['signing_secret'];
        }

        $response = $this->request('PUT', $uri, [
            'body' => \json_encode($data),
        ]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), Webhook::class);

        if (!$result instanceof WebhookInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return new WebhookDecorator($result, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteWebhook(string $targetUrl): bool
    {
        $response = $this->request('DELETE', 'webhooks/unsubscribe/', [
            'body' => \json_encode(['target_url' => $targetUrl]),
        ]);

        return $response->getStatusCode() === 204;
    }
}
