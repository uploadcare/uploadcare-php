<?php

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\AudioInterface;
use Uploadcare\Interfaces\File\VideoInfoInterface;
use Uploadcare\Interfaces\File\VideoInterface;

/**
 * Video Info.
 */
final class VideoInfo implements VideoInfoInterface
{
    /**
     * @var int
     */
    private $duration;

    /**
     * @var string
     */
    private $format;

    /**
     * @var int
     */
    private $bitrate;

    /**
     * @var VideoInterface
     */
    private $video;

    /**
     * @var AudioInterface
     */
    private $audio;

    public function __construct()
    {
        $this->video = new Video();
        $this->audio = new Audio();
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return VideoInfo
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return VideoInfo
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return int
     */
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * @param int $bitrate
     *
     * @return VideoInfo
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    /**
     * @return VideoInterface
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param VideoInterface $video
     *
     * @return VideoInfo
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return AudioInterface
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * @param AudioInterface $audio
     *
     * @return VideoInfo
     */
    public function setAudio($audio)
    {
        $this->audio = $audio;

        return $this;
    }
}
