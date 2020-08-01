<?php

namespace Uploadcare;

use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;

class WebhookCollection extends AbstractCollection
{
    /**
     * @var Response\WebhookCollection|CollectionInterface
     */
    private $inner;

    /**
     * @var WebhookApiInterface
     */
    private $api;

    public function __construct(CollectionInterface $inner, WebhookApiInterface $api)
    {
        $this->inner = $inner;
        $this->api = $api;
        $this->decorateElements();
    }

    private function decorateElements()
    {
        foreach ($this->inner->toArray() as $k => $value) {
            if ($value instanceof WebhookInterface) {
                $this->elements[$k] = new Webhook($value, $this->api);
            }
        }
    }

    protected function createFrom(array $elements)
    {
        return new static(new Response\WebhookCollection($elements), $this->api);
    }

    public static function elementClass()
    {
        return Webhook::class;
    }
}
