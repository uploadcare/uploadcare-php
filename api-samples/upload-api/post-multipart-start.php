<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);

$path = '/path-to-large-file.zip';
$handle = \fopen($path, 'rb');
$response = $uploader->fromResource($handle, null, null, 'auto', [
    'size' => \filesize($path), // The "size" is the metadata key
]);

echo \sprintf('File uploaded. ID is \'%s\'', $response->getUuid());
