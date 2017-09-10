<?php
namespace Uploadcare;

class FileIterator extends PagedDataIterator
{
    public function _pd_getDataChunk($api, $options, $reverse)
    {
        return $api->getFilesChunk($options, $reverse);
    }

    public function _pd_GetCount($api, $options)
    {
        return $api->getFilesCount($options);
    }
}
