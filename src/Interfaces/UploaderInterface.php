<?php

namespace Uploadcare\Interfaces;

use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\File\FileInfoInterface;

interface UploaderInterface
{
    public const UPLOAD_BASE_URL = 'upload.uploadcare.com';
    public const UPLOADCARE_PUB_KEY_KEY = 'UPLOADCARE_PUB_KEY';
    public const UPLOADCARE_STORE_KEY = 'UPLOADCARE_STORE';
    public const UPLOADCARE_SIGNATURE_KEY = 'signature';
    public const UPLOADCARE_EXPIRE_KEY = 'expire';
    public const UPLOADCARE_DEFAULT_STORE = 'auto';

    /**
     * Upload file from local path.
     *
     * @param string      $path
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     * @throws InvalidArgumentException
     *
     * @return FileInfoInterface
     */
    public function fromPath(string $path, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;

    /**
     * Upload file from remote URL.
     *
     * @param string      $url
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     * @throws InvalidArgumentException
     *
     * @return FileInfoInterface
     */
    public function fromUrl(string $url, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;

    /**
     * Upload file from resource opened by `\fopen()`.
     *
     * @param resource    $handle
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     * @throws InvalidArgumentException
     *
     * @return FileInfoInterface
     */
    public function fromResource($handle, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;

    /**
     * Upload file from content string.
     *
     * @param string      $content
     * @param string|null $mimeType
     * @param string|null $filename
     * @param string      $store
     * @throws InvalidArgumentException
     *
     * @return FileInfoInterface
     */
    public function fromContent(string $content, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;
}
