<?php

namespace Uploadcare\Interfaces\Response;

use Uploadcare\Interfaces\Api\FileApiInterface;

/**
 * Response for Copy file to remote storage.
 *
 * @see FileApiInterface::copyToRemoteStorage()
 * @see
 */
interface RemoteStorageResponse
{
    /**
     * @return string
     */
    public function getType();

    /**
     * For the url type, the result is a URL with the s3 scheme. Your bucket name is put as a host, and an s3 object path follows.
     *
     * @return string
     */
    public function getResult();
}
