<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

use Uploadcare\Interfaces\File\{CollectionInterface, FileInfoInterface};

/**
 * Group of files.
 */
interface GroupInterface
{
    /**
     * Group identifier.
     */
    public function getId(): ?string;

    /**
     * Date and time when a group was created.
     */
    public function getDatetimeCreated(): ?\DateTimeInterface;

    /**
     * Date and time when files in a group were stored.
     */
    public function getDatetimeStored(): ?\DateTimeInterface;

    /**
     * Number of files in a group.
     */
    public function getFilesCount(): int;

    /**
     * Public CDN URL for a group.
     */
    public function getCdnUrl(): string;

    /**
     * API resource URL for a group.
     */
    public function getUrl(): string;

    /**
     * @return CollectionInterface<array-key, FileInfoInterface>
     */
    public function getFiles(): CollectionInterface;
}
