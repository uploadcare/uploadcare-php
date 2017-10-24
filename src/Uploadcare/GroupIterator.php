<?php

namespace Uploadcare;

class GroupIterator extends PagedDataIterator
{
    public function pdGetDataChunk($api, $options, $reverse)
    {
        return $api->getGroupsChunk($options, $reverse);
    }

    public function pdGetCount($api, $options)
    {
        return $api->getGroupsCount($options);
    }
}
