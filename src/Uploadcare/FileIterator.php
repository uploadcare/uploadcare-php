<?php

namespace Uploadcare;

class FileIterator extends PagedDataIterator
{
    public function pdGetDataChunk($api, $options, $reverse)
    {
        return $api->getFilesChunk($options, $reverse);
    }

    public function pdGetCount($api, $options)
    {
        return $api->getFilesCount($options);
    }
}
