<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->metadata();
$metadata = $api->getMetadata('1bac376c-aa7e-4356-861b-dd2657b5bfd2');

echo \sprintf('Value for key \'pet\' %s', $metadata['pet'] ?? 'does not exists');
