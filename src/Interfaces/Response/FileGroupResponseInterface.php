<?php

namespace Uploadcare\Interfaces\Response;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * File group response.
 *
 * @see https://uploadcare.com/api-refs/upload-api/#operation/createFilesGroup
 * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/groupsList
 */
interface FileGroupResponseInterface
{
    /**
     * @return string group ID
     */
    public function getId();

    /**
     * @return \DateTimeInterface when group was created
     */
    public function getDatetimeCreated();

    /**
     * @return \DateTimeInterface when group was stored
     */
    public function getDatetimeStored();

    /**
     * @return int number of files in group
     */
    public function getFilesCount();

    /**
     * @return string CDN URL of the group
     */
    public function getCdnUrl();

    /**
     * @return string group API url - to get this info
     */
    public function getUrl();

    /**
     * @return CollectionInterface
     */
    public function getFiles();
}
