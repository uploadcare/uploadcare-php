<?php declare(strict_types=1);

namespace Uploadcare\File\ContentInfo;

use Uploadcare\Interfaces\File\ContentInfo\VideoInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Video.
 */
final class Video implements VideoInterface, SerializableInterface
{
    private int $height = 0;
    private int $width = 0;
    private float $frameRate = .0;
    private int $bitrate = 0;
    private ?string $codec = null;

    /**
     * {@inheritDoc}
     */
    public static function rules(): array
    {
        return [
            'height' => 'int',
            'width' => 'int',
            'frameRate' => 'float',
            'bitrate' => 'int',
            'codec' => 'string',
        ];
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getFrameRate(): float
    {
        return $this->frameRate;
    }

    public function setFrameRate(float $frameRate): self
    {
        $this->frameRate = $frameRate;

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

    public function getCodec(): ?string
    {
        return $this->codec;
    }

    public function setCodec(string $codec): self
    {
        $this->codec = $codec;

        return $this;
    }
}
