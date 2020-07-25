<?php

namespace Tests;

class DataFile
{
    public static function contents($path)
    {
        return \file_get_contents(__DIR__ . '/_data/' . $path);
    }
}
