<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class FileListResponse implements ListResponseInterface, SerializableInterface
{
    private ?string $next = null;

    private ?string $previous = null;

    private int $total = 0;

    private int $perPage = 10;

    /**
     * @var CollectionInterface|FileCollection
     */
    private CollectionInterface $results;

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
     * {@inheritDoc}
     */
    public function getNext(): ?string
    {
        return $this->next;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * {@inheritDoc}
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getResults(): CollectionInterface
    {
        return $this->results;
    }

    public function setNext(?string $next): self
    {
        $this->next = $next;

        return $this;
    }

    public function setPrevious(?string $previous): self
    {
        $this->previous = $previous;

        return $this;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function addResult(?FileInfoInterface $result): self
    {
        if ($result !== null && !$this->results->contains($result)) {
            $this->results->add($result);
        }

        return $this;
    }

    public function setResults(CollectionInterface $results): self
    {
        $this->results = $results;

        return $this;
    }
}
