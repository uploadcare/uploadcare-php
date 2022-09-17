<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\AudioInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Audio.
 */
final class Audio implements AudioInterface, SerializableInterface
{
    private ?int $bitrate = null;
    private ?string $codec = null;
    private ?int $sampleRate = null;
    private ?string $channels = null;

    /**
     * {@inheritDoc}
     */
    public static function rules(): array
    {
        return [
            'bitrate' => 'int',
            'codec' => 'string',
            'sampleRate' => 'int',
            'channels' => 'string',
        ];
    }

    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    public function setBitrate(?int $bitrate): self
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    public function getCodec(): ?string
    {
        return $this->codec;
    }

    public function setCodec(?string $codec): self
    {
        $this->codec = $codec;

        return $this;
    }

    public function getSampleRate(): ?int
    {
        return $this->sampleRate;
    }

    public function setSampleRate(?int $sampleRate): self
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }

    public function getChannels(): ?string
    {
        return $this->channels;
    }

    public function setChannels(?string $channels): self
    {
        $this->channels = $channels;

        return $this;
    }
}
