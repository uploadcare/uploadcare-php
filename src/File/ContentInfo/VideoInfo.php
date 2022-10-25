<?php declare(strict_types=1);

namespace Uploadcare\File\ContentInfo;

use Uploadcare\Interfaces\File\ContentInfo\{AudioInterface, VideoInfoInterface, VideoInterface};
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Video Info.
 */
final class VideoInfo implements VideoInfoInterface, SerializableInterface
{
    private int $duration = 0;
    private ?string $format = null;
    private int $bitrate = 0;
    private VideoInterface $video;
    private AudioInterface $audio;

    public function __construct()
    {
        $this->video = new Video();
        $this->audio = new Audio();
    }

    /**
     * {@inheritDoc}
     */
    public static function rules(): array
    {
        return [
            'duration' => 'int',
            'format' => 'string',
            'bitrate' => 'int',
            'video' => Video::class,
            'audio' => Audio::class,
        ];
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getBitrate(): int
    {
        return $this->bitrate;
    }

    public function setBitrate(int $bitrate): self
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    public function getVideo(): VideoInterface
    {
        return $this->video;
    }

    public function setVideo(VideoInterface $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getAudio(): AudioInterface
    {
        return $this->audio;
    }

    public function setAudio(AudioInterface $audio): self
    {
        $this->audio = $audio;

        return $this;
    }
}
