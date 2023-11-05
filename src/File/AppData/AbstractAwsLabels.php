<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

abstract class AbstractAwsLabels
{
    protected ?string $version = null;
    protected ?\DateTimeInterface $datetimeCreated = null;
    protected ?\DateTimeInterface $datetimeUpdated = null;

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getDatetimeCreated(): ?\DateTimeInterface
    {
        return $this->datetimeCreated;
    }

    public function setDatetimeCreated(?\DateTimeInterface $datetimeCreated): self
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    public function getDatetimeUpdated(): ?\DateTimeInterface
    {
        return $this->datetimeUpdated;
    }

    public function setDatetimeUpdated(?\DateTimeInterface $datetimeUpdated): self
    {
        $this->datetimeUpdated = $datetimeUpdated;

        return $this;
    }
}
