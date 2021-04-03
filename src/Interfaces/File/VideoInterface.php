<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File;

/**
 * Video stream metadata.
 */
interface VideoInterface
{
    /**
     * Video stream image height.
     *
     * @return int
     */
    public function getHeight(): int;

    /**
     * Video stream image width.
     *
     * @return int
     */
    public function getWidth(): int;

    /**
     * Video stream frame rate.
     *
     * @return float
     */
    public function getFrameRate(): float;

    /**
     * Video stream bitrate.
     *
     * @return int
     */
    public function getBitrate(): int;

    /**
     * Video stream codec.
     *
     * @return string
     */
    public function getCodec(): string;
}
