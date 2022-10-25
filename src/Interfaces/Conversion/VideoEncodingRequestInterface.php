<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

/**
 * Request for video encoding.
 */
interface VideoEncodingRequestInterface extends ConversionRequestInterface
{
    public function getHorizontalSize(): ?int;

    public function getVerticalSize(): ?int;

    public function getResizeMode(): ?string;

    public function getQuality(): ?string;

    public function getStartTime(): ?string;

    public function getEndTime(): ?string;

    public function getThumbs(): int;
}
