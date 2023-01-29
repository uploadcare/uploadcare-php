<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);
$fileInfo = $uploader->fromPath(__DIR__ . '/squirrel.jpg', null, null, '1', [
    'system' => 'php-uploader',
    'pet' => 'cat',
]);

echo \sprintf("URL: %s, ID: %s, Mime type: %s\n", $fileInfo->getUrl(), $fileInfo->getUuid(), $fileInfo->getMimeType());
foreach ($fileInfo->getMetadata() as $key => $value) {
    echo \sprintf("%s: %s\n", $key, $value);
}
