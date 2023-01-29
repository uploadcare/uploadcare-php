<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\File\Metadata;
use Uploadcare\Interfaces\File\FileInfoInterface;

interface MetadataApiInterface
{
    /**
     * @param string|FileInfoInterface $id Uuid of File
     *
     * @return Metadata ArrayAccess
     */
    public function getMetadata($id): Metadata;

    /**
     * @param string|FileInfoInterface $id    Uuid of File
     * @param string                   $key   String up to 64 characters
     * @param string                   $value String up to 512 characters
     *
     * @return Metadata ArrayAccess
     */
    public function setKey($id, string $key, string $value): Metadata;

    /**
     * @param string|FileInfoInterface $id  Uuid of File
     * @param string                   $key String up to 64 characters
     */
    public function removeKey($id, string $key): void;
}
