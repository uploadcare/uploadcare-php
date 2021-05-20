<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

/**
 * Request for video encoding.
 */
interface VideoEncodingRequestInterface extends ConversionRequestInterface
{
    /**
     * @return int|null
     */
    public function getHorizontalSize(): ?int;

    /**
     * @return int|null
     */
    public function getVerticalSize(): ?int;

    /**
     * @return string|null
     */
    public function getResizeMode(): ?string;

    /**
     * @return string|null
     */
    public function getQuality(): ?string;

    /**
     * @return string|null
     */
    public function getStartTime(): ?string;

    /**
     * @return string|null
     */
    public function getEndTime(): ?string;

    /**
     * @return int
     */
    public function getThumbs(): int;
}
