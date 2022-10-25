<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\File\ContentInfo\ContentInfo;
use Uploadcare\Interfaces\File\{ContentInfoInterface, FileInfoInterface};
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
    private bool $isImage = false;
    private bool $isReady = false;
    private string $mimeType = '';
    private ?string $originalFileUrl = null;
    private string $originalFilename = '';
    private int $size = 0;
    private string $url = '';
    private string $uuid = '';
    private ?array $variations = null;
    private string $source = '';
    private ?ContentInfoInterface $contentInfo = null;
    private array $metadata = [];
    private ?AppData $appdata = null;

    public static function rules(): array
    {
        return [
            'datetimeRemoved' => \DateTime::class,
            'datetimeStored' => \DateTime::class,
            'datetimeUploaded' => \DateTime::class,
            'isImage' => 'bool',
            'isReady' => 'bool',
            'mimeType' => 'string',
            'originalFilename' => 'string',
            'originalFileUrl' => 'string',
            'size' => 'int',
            'url' => 'string',
            'uuid' => 'string',
            'variations' => 'array',
            'source' => 'string',
            'contentInfo' => ContentInfo::class,
            'metadata' => 'array',
            'appdata' => AppData::class,
        ];
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function getDatetimeRemoved(): ?\DateTimeInterface
    {
        return $this->datetimeRemoved;
    }

    public function setDatetimeRemoved(?\DateTimeInterface $datetimeRemoved = null): self
    {
        $this->datetimeRemoved = $datetimeRemoved;

        return $this;
    }

    public function getDatetimeStored(): ?\DateTimeInterface
    {
        return $this->datetimeStored;
    }

    public function setDatetimeStored(?\DateTimeInterface $datetimeStored = null): self
    {
        $this->datetimeStored = $datetimeStored;

        return $this;
    }

    public function getDatetimeUploaded(): ?\DateTimeInterface
    {
        return $this->datetimeUploaded;
    }

    public function setDatetimeUploaded(?\DateTimeInterface $datetimeUploaded = null): self
    {
        $this->datetimeUploaded = $datetimeUploaded;

        return $this;
    }

    public function isImage(): bool
    {
        return $this->isImage;
    }

    public function setIsImage(bool $isImage): self
    {
        $this->isImage = $isImage;

        return $this;
    }

    public function isReady(): bool
    {
        return $this->isReady;
    }

    public function setIsReady(bool $isReady): self
    {
        $this->isReady = $isReady;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getOriginalFileUrl(): ?string
    {
        return $this->originalFileUrl;
    }

    public function setOriginalFileUrl(?string $originalFileUrl): self
    {
        $this->originalFileUrl = $originalFileUrl;

        return $this;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getVariations(): ?array
    {
        return $this->variations;
    }

    public function setVariations(array $variations = null): self
    {
        $this->variations = $variations;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getMetadata(): Metadata
    {
        return new Metadata($this->metadata);
    }

    public function getContentInfo(): ?ContentInfoInterface
    {
        return $this->contentInfo;
    }

    public function setContentInfo(?ContentInfoInterface $contentInfo): File
    {
        $this->contentInfo = $contentInfo;

        return $this;
    }

    public function setAppdata(?AppData $data): self
    {
        $this->appdata = $data;

        return $this;
    }

    public function getAppdata(): ?AppData
    {
        return $this->appdata;
    }
}
