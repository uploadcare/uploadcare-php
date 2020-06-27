<?php

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
    public function getDuration();

    /**
     * Video format (MP4 for example).
     *
     * @return string
     */
    public function getFormat();

    /**
     * Video bitrate.
     *
     * @return int
     */
    public function getBitrate();

    /**
     * Audio stream metadata.
     *
     * @return AudioInterface|null
     */
    public function getAudio();

    /**
     * Video stream metadata.
     *
     * @return VideoInterface
     */
    public function getVideo();
}
