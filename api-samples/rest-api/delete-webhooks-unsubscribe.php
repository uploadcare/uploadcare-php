<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->webhook();
$result = $api->deleteWebhook('https://yourwebhook.com');

echo $result ? 'Webhook has been deleted' : 'Webhook is not deleted, something went wrong';
