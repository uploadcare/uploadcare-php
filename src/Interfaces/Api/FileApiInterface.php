<?php

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchFileResponseInterface;
use Uploadcare\Interfaces\Response\FileListResponseInterface;

interface FileApiInterface
{
    /**
     * Getting a paginated list of files.
     *
     * @return FileListResponseInterface
     */
    public function listFiles();

    /**
     * Store a single file by UUID.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function storeFile($id);

    /**
     * Remove individual files. Returns file info.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function deleteFile($id);

    /**
     * Specific file info.
     *
     * @param string $id file UUID
     *
     * @return FileInfoInterface
     */
    public function fileInfo($id);

    /**
     * Store multiple files in one step.
     * Up to 100 files are supported per request.
     *
     * @param array $ids array of files UUIDs to store
     *
     * @return BatchFileResponseInterface
     */
    public function batchStoreFile(array $ids);

    /**
     * @param array $ids array of files UUIDs to store
     *
     * @return BatchFileResponseInterface
     */
    public function batchDeleteFile(array $ids);

    /**
     * Copy original files or their modified versions to default storage. Source files MAY either be stored or just uploaded and MUST NOT be deleted.
     *
     * @param string $source a CDN URL or just UUID of a file subjected to copy
     * @param bool   $store  the parameter only applies to the Uploadcare storage and MUST be boolean
     *
     * @return mixed
     */
    public function copyToLocalStorage($source, $store);

    /**
     * @param string $source     a CDN URL or just UUID of a file subjected to copy
     * @param string $target     Identifies a custom storage name related to your project. Implies you are copying a file to a specified custom storage. Keep in mind you can have multiple storage's associated with a single S3 bucket.
     * @param bool   $makePublic true to make copied files available via public links, false to reverse the behavior
     * @param string $pattern    Enum: "${default}" "${auto_filename}" "${effects}" "${filename}" "${uuid}" "${ext}" The parameter is used to specify file names Uploadcare passes to a custom storage. In case the parameter is omitted, we use pattern of your custom storage. Use any combination of allowed values.
     *
     * @return mixed
     */
    public function copyToRemoteStorage($source, $target, $makePublic, $pattern);
}
