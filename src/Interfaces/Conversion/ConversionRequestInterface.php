<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

/**
 * Common conversion request.
 */
interface ConversionRequestInterface
{
    public function getTargetFormat(): string;

    public function throwError(): bool;

    public function store(): bool;

    public function isSaveInGroup(): bool;
}
