<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->webhook();

$result = $api->createWebhook('https://yourwebhook.com', true, 'sign-secret');

echo \sprintf('Webhook %s created', $result->getId());
