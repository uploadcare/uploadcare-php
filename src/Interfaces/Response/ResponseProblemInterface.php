<?php

namespace Uploadcare\Interfaces\Response;

/**
 * Problem with batch file store / delete.
 */
interface ResponseProblemInterface
{
    /**
     * Problem file UUID.
     *
     * @return string
     */
    public function getId();

    /**
     * Problem reason.
     *
     * @return string
     */
    public function getReason();
}
