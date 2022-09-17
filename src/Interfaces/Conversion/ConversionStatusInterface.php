<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

interface ConversionStatusInterface
{
    /**
     * @return string Processing job status, can have one of the following values:
     *                - pending — a source file is being prepared for conversion.
     *                - processing — conversion is in progress.
     *                - finished — the conversion is finished.
     *                - failed — we failed to convert the source, see error for details.
     *                - canceled — the conversion was canceled.
     */
    public function getStatus(): string;

    /**
     * @return string|null holds a conversion error if we were unable to handle your file
     */
    public function getError(): ?string;

    public function getResult(): ?StatusResultInterface;
}
