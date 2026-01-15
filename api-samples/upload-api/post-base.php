<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);
$fileInfo = $uploader->fromPath(__DIR__ . '/squirrel.jpg', null, null, '1', [
    'system' => 'php-uploader',
    'pet' => 'cat',
]);

$data = [
    'url' => $fileInfo->getUrl(),
    'id' => $fileInfo->getUuid(),
    'mimeType' => $fileInfo->getMimeType(),
];
foreach ($fileInfo->getMetadata() as $key => $value) {
    $data[$key] = $value;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
