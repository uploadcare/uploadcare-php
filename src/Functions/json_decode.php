<?php

namespace Uploadcare;

use Uploadcare\Exceptions\JsonException;

function jsonDecode($json, $assoc = false, $depth = 512, $options = 0) {
    $result = \json_decode($json, $assoc, $depth, $options);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $result;
    }

    throw new JsonException(json_last_error_msg(), json_last_error());
}
