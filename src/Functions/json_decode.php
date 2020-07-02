<?php

namespace Uploadcare;

use Uploadcare\Exceptions\JsonException;

function jsonDecode($json, $assoc = false, $depth = 512, $options = 0)
{
    if (PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION <= 3) {
        return \json_decode($json, $assoc, $depth);
    }

    $result = \json_decode($json, $assoc, $depth, $options);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $result;
    }
    if (PHP_MAJOR_VERSION > 5) {
        return false;
    }

    throw new JsonException(json_last_error_msg(), json_last_error());
}
