<?php

namespace Uploadcare\Response;

use Uploadcare\File\UploadedFileCollection;
use Uploadcare\Interfaces\Response\FileGroupResponseInterface;
use Uploadcare\Interfaces\SerializableInterface;
use Uploadcare\Interfaces\UploadedFileInterface;

/**
 * File Group Response.
 */
class FileGroupResponse implements FileGroupResponseInterface, SerializableInterface
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
     * @var \DateTime
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
     * @var UploadedFileCollection
     */
    private $files;

    public function __construct()
    {
        $this->files = new UploadedFileCollection();
    }

    public static function rules()
    {
        return [
            'id' => 'string',
            'datetimeCreated' => \DateTime::class,
            'datetimeStored' => \DateTime::class,
            'filesCount' => 'int',
            'cdnUrl' => 'sting',
            'url' => 'string',
            'files' => UploadedFileCollection::class,
        ];
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
     * @return FileGroupResponse
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
     * @return FileGroupResponse
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeStored()
    {
        return $this->datetimeStored;
    }

    /**
     * @param \DateTime $datetimeStored
     *
     * @return FileGroupResponse
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
     * @return FileGroupResponse
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
     * @return FileGroupResponse
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
     * @return FileGroupResponse
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return UploadedFileCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param UploadedFileInterface $file
     *
     * @return $this
     */
    public function addFile(UploadedFileInterface $file)
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }
}
