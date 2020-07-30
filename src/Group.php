<?php

namespace Uploadcare;

use Uploadcare\Apis\GroupApi;
use Uploadcare\Interfaces\GroupInterface;

/**
 * Decorated Group.
 */
class Group implements GroupInterface
{
    /**
     * @var GroupInterface
     */
    private $inner;

    /**
     * @var GroupApi
     */
    private $api;

    public function __construct(GroupInterface $inner, GroupApi $api)
    {
        $this->inner = $inner;
        $this->api = $api;
    }

    /**
     * @return GroupInterface
     */
    public function store()
    {
        return $this->api->storeGroup($this->inner->getId());
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->inner->getId();
    }

    /**
     * @inheritDoc
     */
    public function getDatetimeCreated()
    {
        return $this->inner->getDatetimeCreated();
    }

    /**
     * @inheritDoc
     */
    public function getDatetimeStored()
    {
        return $this->inner->getDatetimeStored();
    }

    /**
     * @inheritDoc
     */
    public function getFilesCount()
    {
        return $this->inner->getFilesCount();
    }

    /**
     * @inheritDoc
     */
    public function getCdnUrl()
    {
        return $this->inner->getCdnUrl();
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->inner->getUrl();
    }
}
