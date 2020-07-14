<?php

namespace Uploadcare\Interfaces\Response;

use Uploadcare\Interfaces\File\CollectionInterface;

/**
 * File collection representation.
 */
interface FileListResponseInterface
{
    /**
     * @return string|null
     */
    public function getNext();

    /**
     * @return string|null
     */
    public function getPrevious();

    /**
     * @return int
     */
    public function getTotal();

    /**
     * @return int
     */
    public function getPerPage();

    /**
     * @return CollectionInterface
     */
    public function getResults();
}
