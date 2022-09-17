<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;

/**
 * Collection of webhooks.
 */
final class WebhookCollection extends AbstractCollection
{
    private CollectionInterface $inner;
    private WebhookApiInterface $api;

    public function __construct(CollectionInterface $inner, WebhookApiInterface $api)
    {
        $this->elements = [];
        $this->inner = $inner;
        $this->api = $api;
        $this->decorateElements();
    }

    private function decorateElements(): void
    {
        foreach ($this->inner->toArray() as $k => $value) {
            if ($value instanceof WebhookInterface) {
                $this->elements[$k] = new Webhook($value, $this->api);
            }
        }
    }

    /**
     * @return $this|AbstractCollection
     */
    protected function createFrom(array $elements): CollectionInterface
    {
        return new self(new Response\WebhookCollection($elements), $this->api);
    }

    public static function elementClass(): string
    {
        return Webhook::class;
    }
}
