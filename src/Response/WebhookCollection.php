<?php

namespace Uploadcare\Response;

use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\File\CollectionInterface;

class WebhookCollection extends AbstractCollection
{
    /**
     * @var array<array-key, mixed>
     */
    protected $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @inheritDoc
     */
    protected function createFrom(array $elements): CollectionInterface
    {
        return new static($elements);
    }

    /**
     * @inheritDoc
     */
    public static function elementClass(): string
    {
        return WebhookResponse::class;
    }
}
