<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->file();
$fileInfo = $api->fileInfo('1bac376c-aa7e-4356-861b-dd2657b5bfd2');

echo \sprintf('URL: %s, ID: %s, Mime type: %s', $fileInfo->getUrl(), $fileInfo->getUuid(), $fileInfo->getMimeType());
