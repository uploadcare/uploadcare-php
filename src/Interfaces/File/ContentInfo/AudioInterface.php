<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\ContentInfo;

/**
 * Audio stream metadata.
 */
interface AudioInterface
{
    /**
     * Audio stream bitrate.
     */
    public function getBitrate(): ?int;

    /**
     * Audio stream codec.
     */
    public function getCodec(): ?string;

    /**
     * Audio stream sample rate.
     */
    public function getSampleRate(): ?int;

    /**
     * Audio stream number of channels.
     */
    public function getChannels(): ?string;
}
