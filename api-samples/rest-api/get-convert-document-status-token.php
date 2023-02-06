<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->conversion();

$status = $api->documentJobStatus(123456);

echo \sprintf('Conversion status: %s', $status->getError() ?? $status->getStatus());
