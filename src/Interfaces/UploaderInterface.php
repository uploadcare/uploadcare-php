<?php

namespace Uploadcare\Interfaces;

use Uploadcare\Interfaces\Response\FileGroupResponseInterface;

interface UploaderInterface
{
    const UPLOAD_BASE_URL = 'upload.uploadcare.com';
    const UPLOADCARE_PUB_KEY_KEY = 'UPLOADCARE_PUB_KEY';
    const UPLOADCARE_STORE_KEY = 'UPLOADCARE_STORE';
    const UPLOADCARE_SIGNATURE_KEY = 'signature';
    const UPLOADCARE_EXPIRE_KEY = 'expire';
    const UPLOADCARE_DEFAULT_STORE = 'auto';

    /**
     * @param array $files
     *
     * @return FileGroupResponseInterface
     */
    public function groupFiles(array $files);

    /**
     * @param string $groupId
     *
     * @return FileGroupResponseInterface
     */
    public function groupInfo($groupId);

    /**
     * Upload file from local path.
     *
     * @param string      $path
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return UploadedFileInterface
     */
    public function fromPath($path, $mimeType = null, $filename = null, $store = 'auto');

    /**
     * Upload file from remote URL.
     *
     * @param string      $url
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return UploadedFileInterface
     */
    public function fromUrl($url, $mimeType = null, $filename = null, $store = 'auto');

    /**
     * Upload file from resource opened by `\fopen()`.
     *
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return UploadedFileInterface
     */
    public function fromResource($handle, $mimeType = null, $filename = null, $store = 'auto');

    /**
     * Upload file from content string.
     *
     * @param string      $content
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return UploadedFileInterface
     */
    public function fromContent($content, $mimeType = null, $filename = null, $store = 'auto');
}
