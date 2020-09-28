<?php

namespace Uploadcare\Interfaces\Conversion;

interface StatusResultInterface
{
    /**
     * @return string a UUID of a converted target file
     */
    public function getUuid();

    /**
     * @return string|null a UUID of a file group with thumbnails for an output video, based on the thumbs operation parameters
     */
    public function getThumbnailsGroupUuid();
}
