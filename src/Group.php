<?php

namespace Uploadcare;

use Uploadcare\Apis\FileApi;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Interfaces\ConfigurationInterface;
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

    /**
     * @var ConfigurationInterface|null
     */
    private $configuration;

    /**
     * @param GroupInterface $inner
     * @param GroupApi       $api
     */
    public function __construct(GroupInterface $inner, GroupApi $api)
    {
        $this->inner = $inner;
        $this->api = $api;
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return $this
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        return $this;
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

    /**
     * @inheritDoc
     */
    public function getFiles()
    {
        if ($this->configuration === null) {
            return $this->inner->getFiles();
        }

        return new FileCollection($this->inner->getFiles(), new FileApi($this->configuration));
    }
}
