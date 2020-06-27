<?php

namespace Uploadcare\Interfaces\Response;

use Uploadcare\Interfaces\File\FileCollectionInterface;

/**
 * Object of batch file store / delete.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/filesStoring
 */
interface BatchFileResponseInterface
{
    /**
     * Response status (usually 'ok').
     *
     * @return string
     */
    public function getStatus();

    /**
     * Dictionary of passed files UUIDs and problems associated with these UUIDs.
     *
     * @return ResponseProblemInterface
     */
    public function getProblems();

    /**
     * @return FileCollectionInterface
     */
    public function getResult();
}
