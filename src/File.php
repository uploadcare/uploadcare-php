<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\File\Metadata;
use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;

/**
 * File decorator.
 */
final class File implements FileInfoInterface
{
    /**
     * @var File\File|FileInfoInterface
     */
    private FileInfoInterface $inner;
    private FileApiInterface $api;

    public function __construct(FileInfoInterface $inner, FileApiInterface $api)
    {
        $this->inner = $inner;
        $this->api = $api;
    }

    public function __toString(): string
    {
        return (string) $this->inner;
    }

    public function store(): FileInfoInterface
    {
        return $this->api->storeFile($this->inner->getUuid());
    }

    public function delete(): FileInfoInterface
    {
        return $this->api->deleteFile($this->inner->getUuid());
    }

    public function copyToLocalStorage(bool $store = true): FileInfoInterface
    {
        return $this->api->copyToLocalStorage($this->inner->getUuid(), $store);
    }

    public function copyToRemoteStorage(string $target, bool $makePublic = true, ?string $pattern = null): string
    {
        return $this->api->copyToRemoteStorage($this->inner->getUuid(), $target, $makePublic, $pattern ?? '');
    }

    public function generateSecureUrl(): ?string
    {
        return $this->api->generateSecureUrl($this->inner);
    }

    public function getDatetimeRemoved(): ?\DateTimeInterface
    {
        return $this->inner->getDatetimeRemoved();
    }

    public function getDatetimeStored(): ?\DateTimeInterface
    {
        return $this->inner->getDatetimeStored();
    }

    public function getDatetimeUploaded(): ?\DateTimeInterface
    {
        return $this->inner->getDatetimeUploaded();
    }

    public function getImageInfo(): ?ImageInfoInterface
    {
        return $this->inner->getImageInfo();
    }

    public function isImage(): bool
    {
        return $this->inner->isImage();
    }

    public function isReady(): bool
    {
        return $this->inner->isReady();
    }

    public function getMimeType(): ?string
    {
        return $this->inner->getMimeType();
    }

    public function getOriginalFileUrl(): ?string
    {
        return $this->inner->getOriginalFileUrl();
    }

    public function getOriginalFilename(): string
    {
        return $this->inner->getOriginalFilename();
    }

    public function getSize(): int
    {
        return $this->inner->getSize();
    }

    public function getUrl(): string
    {
        return $this->inner->getUrl();
    }

    public function getUuid(): string
    {
        return $this->inner->getUuid();
    }

    public function getVariations(): ?array
    {
        return $this->inner->getVariations();
    }

    public function getVideoInfo(): ?VideoInfoInterface
    {
        return $this->inner->getVideoInfo();
    }

    public function getSource(): string
    {
        return $this->inner->getSource();
    }

    public function getRekognitionInfo(): array
    {
        return $this->inner->getRekognitionInfo();
    }

    public function getMetadata(): Metadata
    {
        return $this->api->getMetadata($this->inner);
    }
}
