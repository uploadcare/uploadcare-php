<?php

namespace Uploadcare;

use Uploadcare\Apis\FileApi;
use Uploadcare\Apis\GroupApi;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
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

    public function setConfiguration(ConfigurationInterface $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function store(): GroupInterface
    {
        return $this->api->storeGroup($this->inner->getId());
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }

    public function getDatetimeCreated(): \DateTimeInterface
    {
        return $this->inner->getDatetimeCreated();
    }

    public function getDatetimeStored(): ?\DateTimeInterface
    {
        return $this->inner->getDatetimeStored();
    }

    public function getFilesCount(): int
    {
        return $this->inner->getFilesCount();
    }

    public function getCdnUrl(): string
    {
        return $this->inner->getCdnUrl();
    }

    public function getUrl(): string
    {
        return $this->inner->getUrl();
    }

    public function getFiles(): CollectionInterface
    {
        if ($this->configuration === null) {
            return $this->inner->getFiles();
        }

        return new FileCollection($this->inner->getFiles(), new FileApi($this->configuration));
    }
}
