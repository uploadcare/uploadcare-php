<?php

namespace Uploadcare\Response;

use Uploadcare\File\GroupCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * GroupList Response.
 */
final class GroupListResponse implements ListResponseInterface, SerializableInterface
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
     * @var CollectionInterface|GroupCollection
     */
    private $results;

    public static function rules()
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

    /**
     * @return string|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param string|null $next
     *
     * @return GroupListResponse
     */
    public function setNext($next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param string|null $previous
     *
     * @return GroupListResponse
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return GroupListResponse
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     *
     * @return GroupListResponse
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return CollectionInterface
     */
    public function getResults()
    {
        return $this->results;
    }

    public function addResult($result)
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
        }

        return $this;
    }

    /**
     * @param CollectionInterface $results
     *
     * @return GroupListResponse
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }
}
