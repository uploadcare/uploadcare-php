<?php

namespace Uploadcare\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class FileListResponse implements ListResponseInterface, SerializableInterface
{
    /**
     * @var string|null
     */
    private $next;

    /**
     * @var string|null
     */
    private $previous;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var CollectionInterface|FileCollection
     */
    private $results;

    public function __construct()
    {
        $this->results = new FileCollection();
    }

    public static function rules()
    {
        return [
            'next' => 'string',
            'previous' => 'string',
            'total' => 'int',
            'perPage' => 'int',
            'results' => FileCollection::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @inheritDoc
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param string|null $next
     *
     * @return FileListResponse
     */
    public function setNext($next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @param string|null $previous
     *
     * @return FileListResponse
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @param int $total
     *
     * @return FileListResponse
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param int $perPage
     *
     * @return FileListResponse
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function addResult($result)
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
        }

        return $this;
    }
}
