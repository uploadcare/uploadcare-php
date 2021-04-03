<?php declare(strict_types=1);

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
    private $variations = null;

    /**
     * @var VideoInfoInterface|null
     */
    private $videoInfo = null;

    /**
     * @var string
     */
    private $source = '';

    /**
     * @var array
     */
    private $rekognitionInfo = [];

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
     * {@inheritDoc}
     */
    public function getDatetimeRemoved(): ?\DateTimeInterface
    {
        return $this->datetimeRemoved;
    }

    /**
     * @param \DateTimeInterface|null $datetimeRemoved
     *
     * @return File
     */
    public function setDatetimeRemoved(?\DateTimeInterface $datetimeRemoved = null): self
    {
        $this->datetimeRemoved = $datetimeRemoved;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatetimeStored(): ?\DateTimeInterface
    {
        return $this->datetimeStored;
    }

    /**
     * @param \DateTimeInterface|null $datetimeStored
     *
     * @return File
     */
    public function setDatetimeStored(?\DateTimeInterface $datetimeStored = null): self
    {
        $this->datetimeStored = $datetimeStored;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatetimeUploaded(): ?\DateTimeInterface
    {
        return $this->datetimeUploaded;
    }

    /**
     * @param \DateTimeInterface|null $datetimeUploaded
     *
     * @return File
     */
    public function setDatetimeUploaded(?\DateTimeInterface $datetimeUploaded = null): self
    {
        $this->datetimeUploaded = $datetimeUploaded;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getImageInfo(): ?ImageInfoInterface
    {
        return $this->imageInfo;
    }

    /**
     * @param ImageInfoInterface $imageInfo
     *
     * @return File
     */
    public function setImageInfo(?ImageInfoInterface $imageInfo): self
    {
        $this->imageInfo = $imageInfo;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isImage(): bool
    {
        return $this->isImage;
    }

    /**
     * @param bool $isImage
     *
     * @return File
     */
    public function setIsImage(bool $isImage): self
    {
        $this->isImage = (bool) $isImage;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isReady(): bool
    {
        return $this->isReady;
    }

    /**
     * @param bool $isReady
     *
     * @return File
     */
    public function setIsReady(bool $isReady): self
    {
        $this->isReady = (bool) $isReady;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalFileUrl(): ?string
    {
        return $this->originalFileUrl;
    }

    /**
     * @param string|null $originalFileUrl
     *
     * @return File
     */
    public function setOriginalFileUrl(?string $originalFileUrl): self
    {
        $this->originalFileUrl = $originalFileUrl;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    /**
     * @param string $originalFilename
     *
     * @return File
     */
    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return File
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return File
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return File
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getVariations(): ?array
    {
        return $this->variations;
    }

    /**
     * @param array|null $variations
     *
     * @return File
     */
    public function setVariations(array $variations = null): self
    {
        $this->variations = $variations;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getVideoInfo(): ?VideoInfoInterface
    {
        return $this->videoInfo;
    }

    /**
     * @param VideoInfoInterface|null $videoInfo
     *
     * @return File
     */
    public function setVideoInfo(VideoInfoInterface $videoInfo = null): self
    {
        $this->videoInfo = $videoInfo;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return File
     */
    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRekognitionInfo(): array
    {
        return $this->rekognitionInfo;
    }

    /**
     * @param array $rekognitionInfo
     *
     * @return File
     */
    public function setRekognitionInfo(array $rekognitionInfo): self
    {
        $this->rekognitionInfo = $rekognitionInfo;

        return $this;
    }
}
