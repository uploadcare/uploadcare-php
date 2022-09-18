<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\{FileInfoInterface, ImageInfoInterface, VideoInfoInterface};
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Class File.
 *
 * @see FileInfoInterface
 */
final class File implements FileInfoInterface, SerializableInterface
{
    private ?\DateTimeInterface $datetimeRemoved = null;
    private ?\DateTimeInterface $datetimeStored = null;
    private ?\DateTimeInterface $datetimeUploaded = null;
    private ?ImageInfoInterface $imageInfo = null;
    private bool $isImage;
    private bool $isReady;
    private string $mimeType;
    private ?string $originalFileUrl;
    private string $originalFilename;
    private int $size;
    private string $url;
    private string $uuid;
    private ?array $variations = null;
    private ?VideoInfoInterface $videoInfo = null;
    private string $source = '';

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

    public function __toString(): string
    {
        return $this->uuid;
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

    public function setIsImage(bool $isImage): self
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isReady(): bool
    {
        return $this->isReady;
    }

    public function setIsReady(bool $isReady): self
    {
        $this->isReady = $isReady;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

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

    public function setRekognitionInfo(array $rekognitionInfo): self
    {
        $this->rekognitionInfo = $rekognitionInfo;

        return $this;
    }

    public function getMetadata(): Metadata
    {
        throw new \BadMethodCallException(\sprintf('Call this method from \'%s\' object', \Uploadcare\File::class));
    }
}
