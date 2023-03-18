<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Apis\{FileApi, GroupApi};
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\{ConfigurationInterface, GroupInterface};

/**
 * Decorated Group.
 */
final class Group implements GroupInterface
{
    private GroupInterface $inner;

    private ?ConfigurationInterface $configuration = null;

    public function __construct(GroupInterface $inner)
    {
        $this->inner = $inner;
    }

    public function setConfiguration(ConfigurationInterface $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->inner->getId();
    }

    public function getDatetimeCreated(): ?\DateTimeInterface
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
