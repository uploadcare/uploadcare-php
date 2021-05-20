<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Audio stream metadata.
 */
interface AudioInterface
{
    /**
     * Audio stream bitrate.
     *
     * @return int|null
     */
    public function getBitrate(): ?int;

    /**
     * Audio stream codec.
     *
     * @return string|null
     */
    public function getCodec(): ?string;

    /**
     * Audio stream sample rate.
     *
     * @return int|null
     */
    public function getSampleRate(): ?int;

    /**
     * Audio stream number of channels.
     *
     * @return string|null
     */
    public function getChannels(): ?string;
}
