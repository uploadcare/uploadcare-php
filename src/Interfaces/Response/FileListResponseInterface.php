<?php

namespace Uploadcare\Interfaces\Response;

use Uploadcare\Interfaces\File\FileCollectionInterface;

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
     * @return FileCollectionInterface
     */
    public function getFiles();
}
