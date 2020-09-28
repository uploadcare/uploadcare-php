<?php

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
    public function getBitrate();

    /**
     * Audio stream codec.
     *
     * @return string|null
     */
    public function getCodec();

    /**
     * Audio stream sample rate.
     *
     * @return int|null
     */
    public function getSampleRate();

    /**
     * Audio stream number of channels.
     *
     * @return string|null
     */
    public function getChannels();
}
