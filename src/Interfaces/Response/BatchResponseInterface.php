<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * Object of batch file store / delete.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/filesStoring
 */
interface BatchResponseInterface
{
    /**
     * Response status (usually 'ok').
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Dictionary of passed files UUIDs and problems associated with these UUIDs.
     *
     * @return ResponseProblemInterface[]
     */
    public function getProblems(): array;

    /**
     * @return CollectionInterface
     */
    public function getResult(): CollectionInterface;
}
