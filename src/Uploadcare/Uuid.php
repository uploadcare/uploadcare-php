<?php

namespace Uploadcare;

use Exception;

/**
 * Class Uuid.
 *
 * @see https://github.com/symfony/polyfill-uuid
 */
class Uuid
{
    public static function create()
    {
        $template = '%08s-%04s-1%03s-%04x-%012s';
        $time = \microtime(false);
        $time = \substr($time, 11).substr($time, 2, 7);

        try {
            $node = \sprintf('%06x%06x', \random_int(0, 0xffffff) | 0x010000, \random_int(0, 0xffffff));
            $clockSeq = \random_int(0, 0x3fff);
        } catch (Exception $e) {
            $node = \sprintf('%06x%06x', \mt_rand(0, 0xffffff) | 0x010000, \mt_rand(0, 0xffffff));
            $clockSeq = \mt_rand(0, 0x3fff);
        }

        return \vsprintf($template, array(
            // 32 bits for "time_low"
            \substr($time, -8),
            // 16 bits for "time_mid"
            \substr($time, -12, 4),
            // 16 bits for "time_hi_and_version",
            \substr($time, -15, 3),
            // 16 bits
            $clockSeq | 0x8000,
            // 48 bits for "node"
            $node,
        ));
    }
}
