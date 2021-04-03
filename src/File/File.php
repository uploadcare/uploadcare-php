<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Class File.
 *
 * @see FileInfoInterface
 */
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
     * @var array|null
     */
    private $variations;

    /**
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
        $this->originalFileUrl = '';
        $this->size = 0;
        $this->url = '';
        $this->uuid = '';
    }

    public static function rules(): array
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
            'originalFileUrl' => 'string',
            'size' => 'int',
            'url' => 'string',
            'uuid' => 'string',
            'variations' => 'array',
            'videoInfo' => VideoInfo::class,
            'source' => 'string',
            'rekognitionInfo' => 'array',
        ];
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getVariations()
    {
        return $this->variations;
    }

    /**
     * @param array|null $variations
     *
     * @return File
     */
    public function setVariations(array $variations = null)
    {
        $this->variations = $variations;

        return $this;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
