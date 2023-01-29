<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->addons();
$status = $api->checkAntivirusScan('request-id');

echo \sprintf('Antivirus scan status: %s', $status);
