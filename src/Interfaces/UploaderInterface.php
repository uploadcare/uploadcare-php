<?php

namespace Uploadcare\Interfaces;

use Uploadcare\Interfaces\File\FileInfoInterface;

interface UploaderInterface
{
    const UPLOAD_BASE_URL = 'upload.uploadcare.com';
    const UPLOADCARE_PUB_KEY_KEY = 'UPLOADCARE_PUB_KEY';
    const UPLOADCARE_STORE_KEY = 'UPLOADCARE_STORE';

    /**
     * Upload file from local path.
     *
     * @param string      $path
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     *
     * @return FileInfoInterface
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
     * @return FileInfoInterface
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
     * @return FileInfoInterface
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
     * @return FileInfoInterface
     */
    public function fromContent($content, $mimeType = null, $filename = null, $store = 'auto');
}
