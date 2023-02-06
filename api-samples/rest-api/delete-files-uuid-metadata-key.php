<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$metadataApi = (new Uploadcare\Api($configuration))->metadata();
try {
    $metadataApi->removeKey('1bac376c-aa7e-4356-861b-dd2657b5bfd2', 'pet');
} catch (\Throwable $e) {
    echo \sprintf('Error while key removing: %s', $e->getMessage());
}
echo 'Key was successfully removed';
