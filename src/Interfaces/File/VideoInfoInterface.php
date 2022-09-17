<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Video metadata.
 */
interface VideoInfoInterface
{
    /**
     * Video duration in milliseconds.
     */
    public function getDuration(): int;

    /**
     * Video format (MP4 for example).
     */
    public function getFormat(): ?string;

    /**
     * Video bitrate.
     */
    public function getBitrate(): int;

    /**
     * Audio stream metadata.
     */
    public function getAudio(): ?AudioInterface;

    /**
     * Video stream metadata.
     */
    public function getVideo(): VideoInterface;
}
