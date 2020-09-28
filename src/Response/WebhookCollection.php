<?php

namespace Uploadcare\Response;

use Uploadcare\File\AbstractCollection;

class WebhookCollection extends AbstractCollection
{
    /**
     * @var array<array-key, \Uploadcare\Interfaces\Response\WebhookInterface>
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @inheritDoc
     */
    protected function createFrom(array $elements)
    {
        return new static($elements);
    }

    /**
     * @inheritDoc
     */
    public static function elementClass()
    {
        return WebhookResponse::class;
    }
}
