<?php

namespace Uploadcare\Interfaces\Conversion;

/**
 * Request for document conversion.
 */
interface DocumentConversionRequestInterface extends ConversionRequest
{
    /**
     * @return int|null
     */
    public function getPageNumber();
}
