<?php

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
    public function getHeight();

    /**
     * Video stream image width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Video stream frame rate.
     *
     * @return float
     */
    public function getFrameRate();

    /**
     * Video stream bitrate.
     *
     * @return int
     */
    public function getBitrate();

    /**
     * Video stream codec.
     *
     * @return string
     */
    public function getCodec();
}
