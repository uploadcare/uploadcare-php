<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Uploadcare API file representation.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/fileInfo
 */
interface FileInfoInterface
{
    /**
     * Date and time when a file was removed, if any.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeRemoved(): ?\DateTimeInterface;

    /**
     * Date and time of the last store request, if any.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeStored(): ?\DateTimeInterface;

    /**
     * Date and time when a file was uploaded.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeUploaded(): ?\DateTimeInterface;

    /**
     * Image metadata.
     *
     * @return ImageInfoInterface|null
     */
    public function getImageInfo(): ?ImageInfoInterface;

    /**
     * Is file is image.
     *
     * @return bool
     */
    public function isImage(): bool;

    /**
     * Is file is ready to be used after upload.
     *
     * @return bool
     */
    public function isReady(): bool;

    /**
     * File MIME-type.
     *
     * @return string|null
     */
    public function getMimeType(): ?string;

    /**
     * Publicly available file CDN URL. Available if a file is not deleted.
     *
     * @return string|null
     */
    public function getOriginalFileUrl(): ?string;

    /**
     * Original file name taken from uploaded file.
     *
     * @return string
     */
    public function getOriginalFilename(): string;

    /**
     * File size in bytes.
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * API resource URL for a particular file.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * File UUID.
     *
     * @return string
     */
    public function getUuid(): string;

    /**
     * Dictionary of other files that has been created using this file as source. Used for video, document and etc. conversion.
     *
     * @return array<array-key, string>|null
     */
    public function getVariations(): ?array;

    /**
     * Video metadata.
     *
     * @return VideoInfoInterface|null
     */
    public function getVideoInfo(): ?VideoInfoInterface;

    /**
     * File upload source. This field contains information about from where file was uploaded, for example: facebook, gdrive, gphotos, etc.
     *
     * @return string
     */
    public function getSource(): string;

    /**
     * @return array<string, string>
     */
    public function getRekognitionInfo(): array;
}
