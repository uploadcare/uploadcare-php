<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->webhook();

foreach ($api->listWebhooks() as $webhook) {
    \sprintf("Webhook with url %s is %s\n", $webhook->getTargetUrl(), $webhook->isActive() ? 'active' : 'not active');
}
