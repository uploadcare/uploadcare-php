<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Apis\GroupApi;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\GroupInterface;

/**
 * Decorated Group Collection.
 */
final class GroupCollection extends File\AbstractCollection
{
    private CollectionInterface $inner;
    private GroupApi $api;

    public function __construct(CollectionInterface $inner, GroupApi $api)
    {
        $this->elements = [];
        $this->inner = $inner;
        $this->api = $api;
        $this->decorateElements();
    }

    /**
     * Make this element decorated.
     */
    private function decorateElements(): void
    {
        foreach ($this->inner->toArray() as $k => $el) {
            if ($el instanceof GroupInterface) {
                $this->elements[$k] = new Group($el);
            }
        }
    }

    protected function createFrom(array $elements): CollectionInterface
    {
        return new self(new File\GroupCollection($elements), $this->api);
    }

    public static function elementClass(): string
    {
        return Group::class;
    }
}
