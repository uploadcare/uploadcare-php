<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Interfaces\SerializableInterface;
use Uploadcare\Search\SearchQuery;

/**
 * Page of file search results.
 *
 * Results are file info objects. Matched tokens of full-text conditions are available through
 * `getHighlight()`.
 *
 * @see https://uploadcare.com/docs/file-search/
 */
final class SearchResponse implements ListResponseInterface, SerializableInterface
{
    private ?string $next = null;

    private ?string $previous = null;

    private int $total = 0;

    private int $perPage = 20;

    /**
     * @var CollectionInterface|FileCollection
     */
    private CollectionInterface $results;

    /**
     * @var array<string, array>
     */
    private array $highlights = [];

    private ?SearchQuery $query = null;

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
     * Number of matching files. May be approximate for very large result sets.
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

    /**
     * Matched tokens of a result file wrapped in `<em>` tags, e.g.
     * `['original_filename' => ['<em>vacation</em>-photo.jpg']]`. `original_filename` and
     * `detected_mime_type` are lists of strings, `metadata` is keyed by metadata field name.
     * Empty for files matched by filters only.
     *
     * @param string|FileInfoInterface $file Uuid of a result file
     */
    public function getHighlight($file): array
    {
        $uuid = $file instanceof FileInfoInterface ? $file->getUuid() : (string) $file;

        return $this->highlights[$uuid] ?? [];
    }

    /**
     * Query this page was requested with, used to request the next one.
     */
    public function getQuery(): ?SearchQuery
    {
        return $this->query;
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

    /**
     * @param array<string, array> $highlights Highlight of each result keyed by file uuid
     */
    public function setHighlights(array $highlights): self
    {
        $this->highlights = $highlights;

        return $this;
    }

    public function setQuery(?SearchQuery $query): self
    {
        $this->query = $query;

        return $this;
    }
}
