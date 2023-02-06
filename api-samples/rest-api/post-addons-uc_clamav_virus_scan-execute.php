<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->addons();
$resultKey = $api->requestAntivirusScan('1bac376c-aa7e-4356-861b-dd2657b5bfd2');

echo \sprintf('Antivirus scan requested. Key is \'%s\'', $resultKey);
