<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);

$url = 'https://source.unsplash.com/random';
$token = $uploader->fromUrl($url, null, null, 'auto', [
    'action' => 'upload from URL',
    'checkDuplicates' => true,
    'storeDuplicates' => false,
]);

echo \sprintf('Upload from URL \'%s\' has been started. Token is %s', $url, $token);
