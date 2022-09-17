<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\{CollectionInterface, FileInfoInterface};
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Files group.
 */
final class Group implements GroupInterface, SerializableInterface
{
    private ?string $id = null;
    private ?\DateTimeInterface $datetimeCreated = null;
    private ?\DateTimeInterface $datetimeStored = null;
    private int $filesCount = 0;
    private ?string $cdnUrl = null;
    private ?string $url = null;

    /**
     * @var FileCollection|CollectionInterface
     */
    private CollectionInterface $files;

    /**
     * @return array|string[]
     */
    public static function rules(): array
    {
        return [
            'id' => 'string',
            'datetimeCreated' => \DateTime::class,
            'datetimeStored' => \DateTime::class,
            'filesCount' => 'int',
            'cdnUrl' => 'string',
            'url' => 'string',
            'files' => FileCollection::class,
        ];
    }

    public function __construct()
    {
        $this->files = new FileCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDatetimeCreated(): ?\DateTimeInterface
    {
        return $this->datetimeCreated;
    }

    public function setDatetimeCreated(\DateTimeInterface $datetimeCreated): self
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    public function getDatetimeStored(): ?\DateTimeInterface
    {
        return $this->datetimeStored;
    }

    public function setDatetimeStored(?\DateTime $datetimeStored): self
    {
        $this->datetimeStored = $datetimeStored;

        return $this;
    }

    public function getFilesCount(): int
    {
        return $this->filesCount;
    }

    public function setFilesCount(int $filesCount): self
    {
        $this->filesCount = $filesCount;

        return $this;
    }

    public function getCdnUrl(): string
    {
        return $this->cdnUrl ?? '';
    }

    public function setCdnUrl(string $cdnUrl): self
    {
        $this->cdnUrl = $cdnUrl;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url ?? '';
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function addFile(FileInfoInterface $fileInfo): self
    {
        if (!$this->files->contains($fileInfo)) {
            $this->files->add($fileInfo);
        }

        return $this;
    }

    public function getFiles(): CollectionInterface
    {
        return $this->files;
    }
}
