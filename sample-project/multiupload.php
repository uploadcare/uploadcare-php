<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

$api = new \Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
$path = '/path/to/very/big/file.heic';

$result = $api->uploader->multipartUpload($path, 'image/heic-sequence', 'file.heic');

echo $result->getUrl();
