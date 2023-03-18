<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->file();
$fileInfo = $api->copyToLocalStorage('1bac376c-aa7e-4356-861b-dd2657b5bfd2', true);

echo \sprintf('File \'%s\' copied to local storage', $fileInfo->getUuid());
