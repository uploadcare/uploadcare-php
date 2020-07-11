<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;
use Uploadcare\Interfaces\UploadedFileInterface;

/**
 * UploadedFile.
 */
class UploadedFile implements UploadedFileInterface, SerializableInterface
{
    /**
     * @var bool
     */
    private $isStored;

    /**
     * @var int
     */
    private $done;

    /**
     * @var string
     */
    private $fileId;

    /**
     * @var int
     */
    private $total;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var int
     */
    private $size;

    /**
     * @var bool
     */
    private $isImage;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var VideoInfoInterface|null
     */
    private $videoInfo;

    /**
     * @var bool
     */
    private $isReady;

    /**
     * @var string
     */
    private $originalFilename;

    /**
     * @var ImageInfoInterface|null
     */
    private $imageInfo;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $s3Bucket;

    public static function rules()
    {
        return [
            'isStored' => 'bool',
            'done' => 'int',
            'fileId' => 'string',
            'total' => 'int',
            'uuid' => 'string',
            'size' => 'int',
            'isImage' => 'bool',
            'filename' => 'string',
            'videoInfo' => VideoInfo::class,
            'isReady' => 'bool',
            'originalFilename' => 'string',
            'imageInfo' => ImageInfo::class,
            'mimeType' => 'string',
            's3Bucket' => 'string',
        ];
    }

    /**
     * @return bool
     */
    public function isStored()
    {
        return $this->isStored;
    }

    /**
     * @param bool $isStored
     *
     * @return UploadedFile
     */
    public function setIsStored($isStored)
    {
        $this->isStored = $isStored;

        return $this;
    }

    /**
     * @return int
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @param int $done
     *
     * @return UploadedFile
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param string $fileId
     *
     * @return UploadedFile
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return UploadedFile
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return UploadedFile
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return UploadedFile
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->isImage;
    }

    /**
     * @param bool $isImage
     *
     * @return UploadedFile
     */
    public function setIsImage($isImage)
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return UploadedFile
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return VideoInfoInterface|null
     */
    public function getVideoInfo()
    {
        return $this->videoInfo;
    }

    /**
     * @param VideoInfoInterface|null $videoInfo
     *
     * @return UploadedFile
     */
    public function setVideoInfo($videoInfo)
    {
        $this->videoInfo = $videoInfo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        return $this->isReady;
    }

    /**
     * @param bool $isReady
     *
     * @return UploadedFile
     */
    public function setIsReady($isReady)
    {
        $this->isReady = $isReady;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * @param string $originalFilename
     *
     * @return UploadedFile
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    /**
     * @return ImageInfoInterface|null
     */
    public function getImageInfo()
    {
        return $this->imageInfo;
    }

    /**
     * @param ImageInfoInterface|null $imageInfo
     *
     * @return UploadedFile
     */
    public function setImageInfo($imageInfo)
    {
        $this->imageInfo = $imageInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return UploadedFile
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getS3Bucket()
    {
        return $this->s3Bucket;
    }

    /**
     * @param string $s3Bucket
     *
     * @return UploadedFile
     */
    public function setS3Bucket($s3Bucket)
    {
        $this->s3Bucket = $s3Bucket;

        return $this;
    }
}
