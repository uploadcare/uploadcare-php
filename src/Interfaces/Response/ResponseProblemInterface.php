<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

/**
 * Problem with batch file store / delete.
 */
interface ResponseProblemInterface
{
    /**
     * Problem file UUID.
     */
    public function getId(): ?string;

    /**
     * Problem reason.
     */
    public function getReason(): ?string;
}
