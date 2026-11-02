<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\Tags;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Result of a file tags operation.
 *
 * The `added` and `deleted` lists are the difference between the previous and the resulting
 * tag set. Both are empty when nothing has been written, and when the response contains the
 * tag list only (as the `GET` call does).
 *
 * @see https://uploadcare.com/docs/file-tags/
 */
final class TagsResponse implements SerializableInterface
{
    /**
     * @var array<int, string>
     */
    private array $tags = [];

    /**
     * @var array<int, string>
     */
    private array $added = [];

    /**
     * @var array<int, string>
     */
    private array $deleted = [];

    public static function rules(): array
    {
        return [
            'tags' => 'array',
            'added' => 'array',
            'deleted' => 'array',
        ];
    }

    /**
     * Resulting tag list in storage order.
     */
    public function getTags(): Tags
    {
        return new Tags($this->tags);
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Tags that were not previously present and have been added.
     *
     * @return array<int, string>
     */
    public function getAdded(): array
    {
        return $this->added;
    }

    public function setAdded(array $added): self
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Tags that were present and have been removed.
     *
     * @return array<int, string>
     */
    public function getDeleted(): array
    {
        return $this->deleted;
    }

    public function setDeleted(array $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
