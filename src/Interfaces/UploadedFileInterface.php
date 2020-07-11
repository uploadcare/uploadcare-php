<?php

namespace Uploadcare\Interfaces;

use Uploadcare\Interfaces\File\ImageInfoInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;

/**
 * Interface UploadedFileInterface.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/fileUploadInfo
 */
interface UploadedFileInterface
{
    /**
     * Is file stored.
     *
     * @return bool
     */
    public function isStored();

    /**
     * Currently uploaded file size in bytes.
     *
     * @return int
     */
    public function getDone();

    /**
     * @see UploadedFileInterface::getUuid()
     *
     * @return string
     */
    public function getFileId();

    /**
     * @see UploadedFileInterface::getSize()
     *
     * @return int
     */
    public function getTotal();

    /**
     * File UUID.
     *
     * @return string
     */
    public function getUuid();

    /**
     * File size.
     *
     * @return int
     */
    public function getSize();

    /**
     * Is file is image.
     *
     * @return bool
     */
    public function isImage();

    /**
     * Sanitized original filename.
     *
     * @return string
     */
    public function getFilename();

    /**
     * Video Info object.
     *
     * @return VideoInfoInterface|null
     */
    public function getVideoInfo();

    /**
     * Is file is ready to be used after upload.
     *
     * @return bool
     */
    public function isReady();

    /**
     * Original file name taken from uploaded file.
     *
     * @return string
     */
    public function getOriginalFilename();

    /**
     * Image metadata.
     *
     * @return ImageInfoInterface|null
     */
    public function getImageInfo();

    /**
     * File MIME-type.
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Your custom user bucket on which file are stored. Only available of you setup foreign storage bucket for your project.
     *
     * @return string|null
     */
    public function getS3Bucket();
}
