<?php

namespace Uploadcare\Interfaces;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Group of files.
 */
interface GroupInterface
{
    /**
     * Group identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Date and time when a group was created.
     *
     * @return \DateTimeInterface
     */
    public function getDatetimeCreated();

    /**
     * Date and time when files in a group were stored.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeStored();

    /**
     * Number of files in a group.
     *
     * @return int
     */
    public function getFilesCount();

    /**
     * Public CDN URL for a group.
     *
     * @return string
     */
    public function getCdnUrl();

    /**
     * API resource URL for a group.
     *
     * @return string
     */
    public function getUrl();

    /**
     * @return CollectionInterface<array-key, \Uploadcare\Interfaces\File\FileInfoInterface>
     */
    public function getFiles();
}
