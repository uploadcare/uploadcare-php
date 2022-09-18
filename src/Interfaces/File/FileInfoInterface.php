<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

use Uploadcare\File\Metadata;

/**
 * Uploadcare API file representation.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/fileInfo
 */
interface FileInfoInterface extends \Stringable
{
    /**
     * Date and time when a file was removed, if any.
     */
    public function getDatetimeRemoved(): ?\DateTimeInterface;

    /**
     * Date and time of the last store request, if any.
     */
    public function getDatetimeStored(): ?\DateTimeInterface;

    /**
     * Date and time when a file was uploaded.
     */
    public function getDatetimeUploaded(): ?\DateTimeInterface;

    /**
     * Is file is image.
     */
    public function isImage(): bool;

    /**
     * Is file is ready to be used after upload.
     */
    public function isReady(): bool;

    /**
     * File MIME-type.
     */
    public function getMimeType(): ?string;

    /**
     * Publicly available file CDN URL. Available if a file is not deleted.
     */
    public function getOriginalFileUrl(): ?string;

    /**
     * Original file name taken from uploaded file.
     */
    public function getOriginalFilename(): string;

    /**
     * File size in bytes.
     */
    public function getSize(): int;

    /**
     * API resource URL for a particular file.
     */
    public function getUrl(): string;

    /**
     * File UUID.
     */
    public function getUuid(): string;

    /**
     * Dictionary of other files that has been created using this file as source. Used for video, document and etc. conversion.
     *
     * @return array<array-key, string>|null
     */
    public function getVariations(): ?array;

    /**
     * File upload source. This field contains information about from where file was uploaded, for example: facebook, gdrive, gphotos, etc.
     */
    public function getSource(): string;

    /**
     * Information about file content.
     */
    public function getContentInfo(): ?ContentInfoInterface;

    public function getMetadata(): Metadata;
}
