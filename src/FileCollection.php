<?php

namespace Uploadcare;

use Uploadcare\Apis\FileApi;
use Uploadcare\File\AbstractCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;

class FileCollection extends AbstractCollection
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
        $this->inner = $inner;
        $this->api = $api;
        $this->decorateElements();
    }

    /**
     * Make this elements decorated.
     */
    private function decorateElements()
    {
        foreach ($this->inner->toArray() as $k => $element) {
            if ($element instanceof FileInfoInterface) {
                $this->elements[$k] = new File($element, $this->api);
            }
        }
    }

    /**
     * @param array<array-key, object> $elements
     * @return AbstractCollection
     */
    protected function createFrom(array $elements)
    {
        return new File\FileCollection($elements);
    }

    /**
     * @inheritDoc
     */
    public static function elementClass()
    {
        return \Uploadcare\File\File::class;
    }

    public function store()
    {
        return $this->api->batchStoreFile($this->inner);
    }

    public function delete()
    {
        return $this->api->batchDeleteFile($this->inner);
    }
}
