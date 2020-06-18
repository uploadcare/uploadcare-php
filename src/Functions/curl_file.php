<?php

namespace Uploadcare;

function curlFile($path, $mime_type = null, $filename = null)
{
    if (PHP_MINOR_VERSION >=5 && \function_exists('curl_file_create')) {
        return \curl_file_create($path, $mime_type, $filename);
    }

    $file = '@' . $path;
    if ($mime_type !== null) {
        $file .= ';type=' . $mime_type;
    }

    if ($filename !== null) {
        $file .= ';filename=' . $filename;
    }

    return $file;
}
