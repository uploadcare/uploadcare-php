<?php declare(strict_types=1);

namespace Uploadcare\Serializer;

/**
 * Words converter. List of plural and singular world forms.
 */
class WordsConverter
{
    public static function conversions(): array
    {
        return [
            'dates' => 'date',
            'properties' => 'property',
            'results' => 'result',
            'files' => 'file',
            'problems' => 'problem',
        ];
    }
}
