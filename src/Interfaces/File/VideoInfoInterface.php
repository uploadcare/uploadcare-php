<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Video metadata.
 */
interface VideoInfoInterface
{
    /**
     * Video duration in milliseconds.
     *
     * @return int
     */
    public function getDuration(): int;

    /**
     * Video format (MP4 for example).
     *
     * @return string
     */
    public function getFormat(): string;

    /**
     * Video bitrate.
     *
     * @return int
     */
    public function getBitrate(): int;

    /**
     * Audio stream metadata.
     *
     * @return AudioInterface|null
     */
    public function getAudio(): ?AudioInterface;

    /**
     * Video stream metadata.
     *
     * @return VideoInterface
     */
    public function getVideo(): VideoInterface;
}
