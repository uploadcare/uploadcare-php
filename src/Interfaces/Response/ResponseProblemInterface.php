<?php declare(strict_types=1);

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
    public function getId(): string;

    /**
     * Problem reason.
     *
     * @return string
     */
    public function getReason(): string;
}
