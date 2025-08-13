<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\GroupCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * GroupList Response.
 */
final class GroupListResponse implements ListResponseInterface, SerializableInterface
{
    private ?string $next = null;

    private ?string $previous = null;

    private int $total = 0;

    private int $perPage = 10;

    /**
     * @var CollectionInterface|GroupCollection
     */
    private CollectionInterface $results;

    public static function rules(): array
    {
        return [
            'next' => 'string',
            'previous' => 'string',
            'total' => 'int',
            'perPage' => 'int',
            'results' => GroupCollection::class,
        ];
    }

    public function __construct()
    {
        $this->results = new GroupCollection();
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function setNext(?string $next): self
    {
        $this->next = $next;

        return $this;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    public function setPrevious(?string $previous): self
    {
        $this->previous = $previous;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getResults(): CollectionInterface
    {
        return $this->results;
    }

    public function addResult(?GroupInterface $result = null): self
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
