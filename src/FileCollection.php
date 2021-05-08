<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Apis\FileApi;
use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;

final class FileCollection extends AbstractCollection
{
    /**
     * @var File\FileCollection|CollectionInterface
     */
    private $inner;

    /**
     * @var FileApi
     */
    private $api;

    public function __construct(CollectionInterface $inner, FileApi $api)
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
        foreach ($this->inner->toArray() as $k => $element) {
            if ($element instanceof FileInfoInterface) {
                $this->elements[$k] = new File($element, $this->api);
            }
        }
    }

    /**
     * @param array<array-key, FileInfoInterface|mixed> $elements
     *
     * @return CollectionInterface<array-key, FileInfoInterface>
     */
    protected function createFrom(array $elements): CollectionInterface
    {
        return new self(new File\FileCollection($elements), $this->api);
    }

    public static function elementClass(): string
    {
        return File::class;
    }

    public function store(): BatchResponseInterface
    {
        return $this->api->batchStoreFile($this->inner);
    }

    public function delete(): BatchResponseInterface
    {
        return $this->api->batchDeleteFile($this->inner);
    }
}
