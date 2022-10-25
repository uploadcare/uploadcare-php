<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * @psalm-template T of WebhookResponse
 */
final class WebhookCollection extends AbstractCollection
{
    /**
     * @var array<array-key,T>
     *
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    protected function createFrom(array $elements): CollectionInterface
    {
        return new WebhookCollection($elements);
    }

    public static function elementClass(): string
    {
        return WebhookResponse::class;
    }
}
