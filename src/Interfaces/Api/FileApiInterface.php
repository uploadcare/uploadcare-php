<?php

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\AuthUrl\UrlGeneratorInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;

interface FileApiInterface
{
    /**
     * Get the next page from previous answer (if next page exists).
     *
     * @param ListResponseInterface $response
     *
     * @return ListResponseInterface|null
     */
    public function nextPage(ListResponseInterface $response);

    /**
     * Getting a paginated list of files.
     *
     * @param int             $limit     A preferred amount of files in a list for a single response. Defaults to 100, while the maximum is 1000.
     * @param string          $orderBy   specifies the way files are sorted in a returned list
     * @param string|int|null $from      A starting point for filtering files. The value depends on your $orderBy parameter value.
     * @param array           $addFields Add special fields to the file object
     * @param bool|null       $stored    `true` to only include files that were stored, `false` to include temporary ones. The default is unset: both stored and not stored files are returned.
     * @param bool            $removed   `true` to only include removed files in the response, `false` to include existing files. Defaults to false.
     *
     * @return ListResponseInterface
     */
    public function listFiles($limit = 100, $orderBy = 'datetime_uploaded', $from = null, $addFields = [], $stored = null, $removed = false);

    /**
     * Store a single file by UUID.
     *
     * @param string|FileInfoInterface $id file UUID
     *
     * @return FileInfoInterface
     */
    public function storeFile($id);

    /**
     * Remove individual files. Returns file info.
     *
     * @param string|FileInfoInterface $id file UUID
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
     * @param array|CollectionInterface $ids array of files UUIDs or FileCollection to store
     *
     * @return BatchResponseInterface
     */
    public function batchStoreFile($ids);

    /**
     * @param array|CollectionInterface $ids array of files UUIDs to store
     *
     * @return BatchResponseInterface
     */
    public function batchDeleteFile($ids);

    /**
     * Copy original files or their modified versions to default storage. Source files MAY either be stored or just uploaded and MUST NOT be deleted.
     *
     * @param string|FileInfoInterface $source a CDN URL or just UUID of a file subjected to copy
     * @param bool                     $store  the parameter only applies to the Uploadcare storage and MUST be boolean
     *
     * @return mixed
     */
    public function copyToLocalStorage($source, $store);

    /**
     * @param string|FileInfoInterface $source     a CDN URL or just UUID of a file subjected to copy
     * @param string                   $target     Identifies a custom storage name related to your project. Implies you are copying a file to a specified custom storage. Keep in mind you can have multiple storage's associated with a single S3 bucket.
     * @param bool                     $makePublic true to make copied files available via public links, false to reverse the behavior
     * @param string                   $pattern    Enum: "${default}" "${auto_filename}" "${effects}" "${filename}" "${uuid}" "${ext}" The parameter is used to specify file names Uploadcare passes to a custom storage. In case the parameter is omitted, we use pattern of your custom storage. Use any combination of allowed values.
     *
     * @return mixed
     */
    public function copyToRemoteStorage($source, $target, $makePublic, $pattern);

    /**
     * Generate secure URL for CDN custom domain.
     *
     * @param UrlGeneratorInterface    $generator
     * @param FileInfoInterface|string $id
     *
     * @return string|null
     */
    public function generateSecureUrl(UrlGeneratorInterface $generator, $id);
}
