<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\ContentInfo;

/**
 * Video stream metadata.
 */
interface VideoInterface
{
    /**
     * Video stream image height.
     */
    public function getHeight(): int;

    /**
     * Video stream image width.
     */
    public function getWidth(): int;

    /**
     * Video stream frame rate.
     */
    public function getFrameRate(): float;

    /**
     * Video stream bitrate.
     */
    public function getBitrate(): int;

    /**
     * Video stream codec.
     */
    public function getCodec(): ?string;
}
