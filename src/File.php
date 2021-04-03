<?php declare(strict_types=1);

namespace Uploadcare;

use Uploadcare\Apis\FileApi;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;

/**
 * File decorator.
 */
class File implements FileInfoInterface
{
    /**
     * @var File\File|FileInfoInterface
     */
    private $inner;

    /**
     * @var FileApi
     */
    private $api;

    /**
     * @param FileInfoInterface $inner
     * @param FileApi           $api
     */
    public function __construct(FileInfoInterface $inner, FileApi $api)
    {
        $this->inner = $inner;
        $this->api = $api;
    }

    /**
     * @return FileInfoInterface
     */
    public function store(): FileInfoInterface
    {
        return $this->api->storeFile($this->inner->getUuid());
    }

    /**
     * @return FileInfoInterface
     */
    public function delete(): FileInfoInterface
    {
        return $this->api->deleteFile($this->inner->getUuid());
    }

    /**
     * @param bool $store
     *
     * @return FileInfoInterface
     */
    public function copyToLocalStorage($store = true): FileInfoInterface
    {
        return $this->api->copyToLocalStorage($this->inner->getUuid(), $store);
    }

    /**
     * @param string      $target
     * @param bool        $makePublic
     * @param string|null $pattern
     *
     * @return string
     */
    public function copyToRemoteStorage($target, $makePublic = true, $pattern = null): string
    {
        return $this->api->copyToRemoteStorage($this->inner->getUuid(), $target, $makePublic, $pattern);
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
}
