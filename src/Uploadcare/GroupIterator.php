<?php

namespace Uploadcare;

class GroupIterator extends PagedDataIterator
{
    public function _pd_getDataChunk($api, $options, $reverse)
    {
        return $api->getGroupsChunk($options, $reverse);
    }

    public function _pd_GetCount($api, $options)
    {
        return $api->getGroupsCount($options);
    }
}
