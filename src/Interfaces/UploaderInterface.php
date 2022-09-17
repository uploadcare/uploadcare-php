<?php declare(strict_types=1);

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
     * @throws InvalidArgumentException
     */
    public function fromPath(string $path, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;

    /**
     * Upload file from remote URL.
     *
     * @throws InvalidArgumentException
     */
    public function fromUrl(string $url, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;

    /**
     * Upload file from resource opened by `\fopen()`.
     *
     * @param resource $handle
     *
     * @throws InvalidArgumentException
     */
    public function fromResource($handle, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;

    /**
     * Upload file from content string.
     *
     * @throws InvalidArgumentException
     */
    public function fromContent(string $content, string $mimeType = null, string $filename = null, string $store = 'auto'): FileInfoInterface;
}
