<?php declare(strict_types=1);

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
    public function getId(): string;

    /**
     * Date and time when a group was created.
     *
     * @return \DateTimeInterface
     */
    public function getDatetimeCreated(): \DateTimeInterface;

    /**
     * Date and time when files in a group were stored.
     *
     * @return \DateTimeInterface|null
     */
    public function getDatetimeStored(): ?\DateTimeInterface;

    /**
     * Number of files in a group.
     *
     * @return int
     */
    public function getFilesCount(): int;

    /**
     * Public CDN URL for a group.
     *
     * @return string
     */
    public function getCdnUrl(): string;

    /**
     * API resource URL for a group.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return CollectionInterface<array-key, \Uploadcare\Interfaces\File\FileInfoInterface>
     */
    public function getFiles(): CollectionInterface;
}
