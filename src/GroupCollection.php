<?php declare(strict_types=1);

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
        $this->elements = [];
        $this->inner = $inner;
        $this->api = $api;
        $this->decorateElements();
    }

    /**
     * Make this elements decorated.
     */
    private function decorateElements(): void
    {
        foreach ($this->inner->toArray() as $k => $el) {
            if ($el instanceof GroupInterface) {
                $this->elements[$k] = new Group($el, $this->api);
            }
        }
    }

    protected function createFrom(array $elements): CollectionInterface
    {
        return new static(new File\GroupCollection($elements), $this->api);
    }

    public static function elementClass(): string
    {
        return Group::class;
    }
}
