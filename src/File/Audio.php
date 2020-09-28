<?php

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
     * @inheritDoc
     */
    public static function rules()
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
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * @param int|null $bitrate
     *
     * @return Audio
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * @param string|null $codec
     *
     * @return Audio
     */
    public function setCodec($codec)
    {
        $this->codec = $codec;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSampleRate()
    {
        return $this->sampleRate;
    }

    /**
     * @param int|null $sampleRate
     *
     * @return Audio
     */
    public function setSampleRate($sampleRate)
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param string|null $channels
     *
     * @return Audio
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;

        return $this;
    }
}
