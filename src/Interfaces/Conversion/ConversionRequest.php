<?php

namespace Uploadcare\Interfaces\Conversion;

/**
 * Common conversion request.
 */
interface ConversionRequest
{
    /**
     * @return string
     */
    public function getTargetFormat();

    /**
     * @return bool
     */
    public function throwError();

    /**
     * @return bool
     */
    public function store();
}
