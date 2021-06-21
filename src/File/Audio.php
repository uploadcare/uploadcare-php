<?php declare(strict_types=1);

namespace Uploadcare\File;

use Uploadcare\Interfaces\File\AudioInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Audio.
 */
final class Audio implements AudioInterface, SerializableInterface
{
    /**
     * @var int|null
     */
    private $bitrate;

    /**
     * @var string|null
     */
    private $codec;

    /**
     * @var int|null
     */
    private $sampleRate;

    /**
     * @var string|null
     */
    private $channels;

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

    /**
     * @return int|null
     */
    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    /**
     * @param int|null $bitrate
     *
     * @return Audio
     */
    public function setBitrate(?int $bitrate): self
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodec(): ?string
    {
        return $this->codec;
    }

    /**
     * @param string|null $codec
     *
     * @return Audio
     */
    public function setCodec(?string $codec): self
    {
        $this->codec = $codec;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSampleRate(): ?int
    {
        return $this->sampleRate;
    }

    /**
     * @param int|null $sampleRate
     *
     * @return Audio
     */
    public function setSampleRate(?int $sampleRate): self
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getChannels(): ?string
    {
        return $this->channels;
    }

    /**
     * @param string|null $channels
     *
     * @return Audio
     */
    public function setChannels(?string $channels): self
    {
        $this->channels = $channels;

        return $this;
    }
}
