<?php

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
    public static function rules()
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Group
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }

    /**
     * @param \DateTime $datetimeCreated
     *
     * @return Group
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDatetimeStored()
    {
        return $this->datetimeStored;
    }

    /**
     * @param \DateTime|null $datetimeStored
     *
     * @return Group
     */
    public function setDatetimeStored($datetimeStored)
    {
        $this->datetimeStored = $datetimeStored;

        return $this;
    }

    /**
     * @return int
     */
    public function getFilesCount()
    {
        return $this->filesCount;
    }

    /**
     * @param int $filesCount
     *
     * @return Group
     */
    public function setFilesCount($filesCount)
    {
        $this->filesCount = $filesCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCdnUrl()
    {
        return $this->cdnUrl;
    }

    /**
     * @param string $cdnUrl
     *
     * @return Group
     */
    public function setCdnUrl($cdnUrl)
    {
        $this->cdnUrl = $cdnUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Group
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function addFile(FileInfoInterface $fileInfo)
    {
        if (!$this->files->contains($fileInfo)) {
            $this->files->add($fileInfo);
        }

        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }
}
