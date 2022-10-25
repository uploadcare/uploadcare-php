<?php declare(strict_types=1);

namespace Tests;

class DataFile
{
    public static function contents(string $path): string
    {
        return \file_get_contents(__DIR__ . '/_data/' . $path);
    }

    public static function fopen(string $path, string $mode)
    {
        return \fopen(__DIR__ . '/_data/' . $path, $mode);
    }
}
