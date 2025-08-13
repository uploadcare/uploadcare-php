<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->group();
try {
    $api->removeGroup('c5bec8c7-d4b6-4921-9e55-6edb027546bc~1');
} catch (Throwable $e) {
    echo \sprintf('Error while group deletion: %s', $e->getMessage());
}
echo 'Group successfully deleted';
