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

    public static function rules(): array
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
    public function getNext(): ?string
    {
        return $this->next;
    }

    /**
     * @inheritDoc
     */
    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return CollectionInterface
     */
    public function getResults(): CollectionInterface
    {
        return $this->results;
    }

    /**
     * @param string|null $next
     *
     * @return FileListResponse
     */
    public function setNext(?string $next): self
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @param string|null $previous
     *
     * @return FileListResponse
     */
    public function setPrevious(?string $previous): self
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @param int $total
     *
     * @return FileListResponse
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param int $perPage
     *
     * @return FileListResponse
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function addResult($result): self
    {
        if ($result !== null && !$this->results->contains($result)) {
            $this->results->add($result);
        }

        return $this;
    }

    /**
     * @param CollectionInterface $results
     *
     * @return self
     */
    public function setResults(CollectionInterface $results): self
    {
        $this->results = $results;

        return $this;
    }
}
