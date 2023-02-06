<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->addons();
$status = $api->checkAwsRecognition('request-id');

echo \sprintf('Recognition status: %s', $status);
