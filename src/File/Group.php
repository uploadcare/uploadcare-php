<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\GroupInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Files group.
 */
final class Group implements GroupInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $datetimeCreated;

    /**
     * @var \DateTime|null
     */
    private $datetimeStored;

    /**
     * @var int
     */
    private $filesCount;

    /**
     * @var string
     */
    private $cdnUrl;

    /**
     * @var string
     */
    private $url;

    /**
     * @var FileCollection|CollectionInterface
     */
    private $files;

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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Group
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeCreated(): \DateTimeInterface
    {
        return $this->datetimeCreated;
    }

    /**
     * @param \DateTime $datetimeCreated
     *
     * @return Group
     */
    public function setDatetimeCreated(\DateTimeInterface $datetimeCreated): self
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDatetimeStored(): ?\DateTimeInterface
    {
        return $this->datetimeStored;
    }

    /**
     * @param \DateTime|null $datetimeStored
     *
     * @return Group
     */
    public function setDatetimeStored(?\DateTime $datetimeStored): self
    {
        $this->datetimeStored = $datetimeStored;

        return $this;
    }

    /**
     * @return int
     */
    public function getFilesCount(): int
    {
        return $this->filesCount;
    }

    /**
     * @param int $filesCount
     *
     * @return Group
     */
    public function setFilesCount(int $filesCount): self
    {
        $this->filesCount = $filesCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCdnUrl(): string
    {
        return $this->cdnUrl;
    }

    /**
     * @param string $cdnUrl
     *
     * @return Group
     */
    public function setCdnUrl(string $cdnUrl): self
    {
        $this->cdnUrl = $cdnUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Group
     */
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
