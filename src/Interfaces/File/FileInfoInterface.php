<?php

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
    public function getDatetimeRemoved();

    /**
     * Date and time of the last store request, if any.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeStored();

    /**
     * Date and time when a file was uploaded.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeUploaded();

    /**
     * Image metadata.
     *
     * @return ImageInfoInterface|null
     */
    public function getImageInfo();

    /**
     * Is file is image.
     *
     * @return bool
     */
    public function isImage();

    /**
     * Is file is ready to be used after upload.
     *
     * @return bool
     */
    public function isReady();

    /**
     * File MIME-type.
     *
     * @return string|null
     */
    public function getMimeType();

    /**
     * Publicly available file CDN URL. Available if a file is not deleted.
     *
     * @return string|null
     */
    public function getOriginalFileUrl();

    /**
     * Original file name taken from uploaded file.
     *
     * @return string
     */
    public function getOriginalFilename();

    /**
     * File size in bytes.
     *
     * @return int
     */
    public function getSize();

    /**
     * API resource URL for a particular file.
     *
     * @return string
     */
    public function getUrl();

    /**
     * File UUID.
     *
     * @return string
     */
    public function getUuid();

    /**
     * Dictionary of other files that has been created using this file as source. Used for video, document and etc. conversion.
     *
     * @return FileCollectionInterface
     */
    public function getVariations();

    /**
     * Video metadata.
     *
     * @return VideoInfoInterface|null
     */
    public function getVideoInfo();

    /**
     * File upload source. This field contains information about from where file was uploaded, for example: facebook, gdrive, gphotos, etc.
     *
     * @return string
     */
    public function getSource();

    /**
     * @return array<string, string>
     */
    public function getRekognitionInfo();
}
