<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\RemoveBgDataInterface;
use Uploadcare\Interfaces\File\AppData\RemoveBgInterface;
use Uploadcare\Interfaces\SerializableInterface;

class RemoveBg implements RemoveBgInterface, SerializableInterface
{
    private ?string $version = null;
    private ?\DateTimeInterface $datetimeCreated = null;
    private ?\DateTimeInterface $datetimeUpdated = null;
    private ?RemoveBgData $data = null;

    public static function rules(): array
    {
        return [
            'version' => 'string',
            'datetimeCreated' => \DateTime::class,
            'datetimeUpdated' => \DateTime::class,
            'data' => RemoveBgData::class,
        ];
    }

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

    public function getData(): ?RemoveBgDataInterface
    {
        return $this->data;
    }

    public function setData(?RemoveBgData $data): self
    {
        $this->data = $data;

        return $this;
    }
}
