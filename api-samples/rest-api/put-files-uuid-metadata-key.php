<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->metadata();
$result = $api->setKey('1bac376c-aa7e-4356-861b-dd2657b5bfd2', 'pet', 'dog');

echo \sprintf('Metadata key \'pet\' is set to %s', $result['pet']);
