<?php

namespace Uploadcare;

use Uploadcare\Apis\GroupApi;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\GroupInterface;

/**
 * Decorated Group Collection.
 */
class GroupCollection extends File\AbstractCollection
{
    /**
     * @var CollectionInterface
     */
    private $inner;

    /**
     * @var GroupApi
     */
    private $api;

    public function __construct(CollectionInterface $inner, GroupApi $api)
    {
        $this->inner = $inner;
        $this->api = $api;
        $this->decorateElements();
    }

    /**
     * Make this elements decorated.
     */
    private function decorateElements()
    {
        foreach ($this->inner->toArray() as $k => $el) {
            if ($el instanceof GroupInterface) {
                $this->elements[$k] = new Group($el, $this->api);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function createFrom(array $elements)
    {
        return new static(new File\GroupCollection($elements), $this->api);
    }

    /**
     * @inheritDoc
     */
    public static function elementClass()
    {
        return Group::class;
    }
}
