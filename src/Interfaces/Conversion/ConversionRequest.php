<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

/**
 * Common conversion request.
 */
interface ConversionRequest
{
    /**
     * @return string
     */
    public function getTargetFormat(): string;

    /**
     * @return bool
     */
    public function throwError(): bool;

    /**
     * @return bool
     */
    public function store(): bool;
}
