<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Conversion;

/**
 * Request for document conversion.
 */
interface DocumentConversionRequestInterface extends ConversionRequestInterface
{
    /**
     * @return int|null
     */
    public function getPageNumber(): ?int;
}
