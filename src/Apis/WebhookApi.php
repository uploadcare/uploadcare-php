<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\{WebhookCollection, WebhookResponse as Webhook};
use Uploadcare\{Webhook as WebhookDecorator, WebhookCollection as WebhookCollectionDecorator};

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
            try {
                $webhookString = \json_encode($webhook, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                continue;
            }

            $obj = $this->configuration->getSerializer()
                ->deserialize($webhookString, Webhook::class);

            if ($obj instanceof Webhook) {
                $result->add($obj);
            }
        }

        return new WebhookCollectionDecorator($result, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function createWebhook(string $targetUrl, bool $isActive = true, ?string $signingSecret = null, string $event = 'file.uploaded'): WebhookInterface
    {
        if ($signingSecret !== null) {
            $signingSecret = \substr($signingSecret, 0, 32);
        }
        try {
            $requestBody = \json_encode([
                'target_url' => $targetUrl,
                'event' => $event,
                'is_active' => $isActive,
                'signing_secret' => $signingSecret,
            ], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $response = $this->request('POST', 'webhooks/', [
            'body' => $requestBody,
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

        try {
            $requestBody = \json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $response = $this->request('PUT', $uri, [
            'body' => $requestBody,
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
        try {
            $requestBody = \json_encode(['target_url' => $targetUrl], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return false;
        }

        $response = $this->request('DELETE', 'webhooks/unsubscribe/', [
            'body' => $requestBody,
        ]);

        return $response->getStatusCode() === 204;
    }
}
