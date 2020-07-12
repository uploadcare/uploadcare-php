<?php

namespace Uploadcare\Serializer;

/**
 * Words converter. List of plural and singular world forms.
 */
class WordsConverter
{
    public static function conversions()
    {
        return [
            'dates' => 'date',
            'properties' => 'property',
        ];
    }
}
