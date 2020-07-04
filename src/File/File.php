<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class File implements FileInfoInterface, SerializableInterface
{
    /**
     * @var \DateTimeInterface|null
     */
    private $datetimeRemoved;

    /**
     * @var \DateTimeInterface|null
     */
    private $datetimeStored;

    /**
     * @var \DateTimeInterface|null
     */
    private $datetimeUploaded;

    /**
     * @var ImageInfoInterface|null
     */
    private $imageInfo;

    /**
     * @var bool
     */
    private $isImage;

    /**
     * @var bool
     */
    private $isReady;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string|null
     */
    private $originalFileUrl;

    /**
     * @var string
     */
    private $originalFilename;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @todo Not a files collection
     * @var CollectionInterface|null
     */
    private $variations;

    /**
     * @todo make it implements SerializableInterface too
     * @var VideoInfoInterface|null
     */
    private $videoInfo;

    /**
     * @var string
     */
    private $source;

    /**
     * @var array
     */
    private $rekognitionInfo;

    public function __construct()
    {
        $this->isImage = false;
        $this->isReady = false;
        $this->mimeType = '';
        $this->originalFilename = '';
        $this->size = 0;
        $this->url = '';
        $this->uuid = '';
    }

    public static function rules()
    {
        return [
            'datetimeRemoved' => \DateTime::class,
            'datetimeStored' => \DateTime::class,
            'datetimeUploaded' => \DateTime::class,
            'imageInfo' => ImageInfo::class,
            'isImage' => 'bool',
            'isReady' => 'bool',
            'mimeType' => 'string',
            'originalFilename' => 'string',
            'size' => 'int',
            'url' => 'string',
            'uuid' => 'string',
//            'variations' => FileCollection::class,
//            'videoInfo' => VideoInfo::class,
            'source' => 'string',
            'rekognitionInfo' => 'array',
        ];
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatetimeRemoved()
    {
        return $this->datetimeRemoved;
    }

    /**
     * @param \DateTimeInterface|null $datetimeRemoved
     *
     * @return File
     */
    public function setDatetimeRemoved(\DateTimeInterface $datetimeRemoved = null)
    {
        $this->datetimeRemoved = $datetimeRemoved;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatetimeStored()
    {
        return $this->datetimeStored;
    }

    /**
     * @param \DateTimeInterface|null $datetimeStored
     *
     * @return File
     */
    public function setDatetimeStored(\DateTimeInterface $datetimeStored = null)
    {
        $this->datetimeStored = $datetimeStored;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatetimeUploaded()
    {
        return $this->datetimeUploaded;
    }

    /**
     * @param \DateTimeInterface|null $datetimeUploaded
     *
     * @return File
     */
    public function setDatetimeUploaded(\DateTimeInterface $datetimeUploaded = null)
    {
        $this->datetimeUploaded = $datetimeUploaded;

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
     * @param ImageInfoInterface $imageInfo
     *
     * @return File
     */
    public function setImageInfo(ImageInfoInterface $imageInfo)
    {
        $this->imageInfo = $imageInfo;

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
     * @return File
     */
    public function setIsImage($isImage)
    {
        $this->isImage = (bool) $isImage;

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
     * @return File
     */
    public function setIsReady($isReady)
    {
        $this->isReady = (bool) $isReady;

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
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = (string) $mimeType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalFileUrl()
    {
        return $this->originalFileUrl;
    }

    /**
     * @param string|null $originalFileUrl
     *
     * @return File
     */
    public function setOriginalFileUrl($originalFileUrl)
    {
        $this->originalFileUrl = $originalFileUrl;

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
     * @return File
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;

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
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

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
     * @return File
     */
    public function setUrl($url)
    {
        $this->url = $url;

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
     * @return File
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return CollectionInterface|null
     */
    public function getVariations()
    {
        return $this->variations;
    }

    /**
     * @param FileCollection|CollectionInterface|null $variations
     *
     * @return File
     */
    public function setVariations(CollectionInterface $variations = null)
    {
        $this->variations = $variations;

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
     * @return File
     */
    public function setVideoInfo(VideoInfoInterface $videoInfo = null)
    {
        $this->videoInfo = $videoInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return File
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getRekognitionInfo()
    {
        return $this->rekognitionInfo;
    }

    /**
     * @param array $rekognitionInfo
     *
     * @return File
     */
    public function setRekognitionInfo($rekognitionInfo)
    {
        $this->rekognitionInfo = $rekognitionInfo;

        return $this;
    }
}
