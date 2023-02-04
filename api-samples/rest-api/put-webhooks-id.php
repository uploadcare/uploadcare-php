<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->webhook();
$webhook = $api->updateWebhook(1234, [
    'target_url' => 'https://yourwebhook.com',
    'event' => 'file.uploaded',
    'is_active' => true,
    'signing_secret' => 'webhook-secret',
]);

\sprintf("Webhook with url %s is %s\n", $webhook->getTargetUrl(), $webhook->isActive() ? 'active' : 'not active');
