<?php

use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->conversion();

$request = (new Uploadcare\Conversion\VideoEncodingRequest())
    ->setHorizontalSize(1024)
    ->setVerticalSize(768)
    ->setResizeMode('preserve_ratio')
    ->setTargetFormat('mp4');

$result = $api->convertVideo('1bac376c-aa7e-4356-861b-dd2657b5bfd2', $request);

if ($result instanceof ConvertedItemInterface) {
    echo \sprintf('Conversion requested. Key is \'%s\'', $result->getToken());
}

if ($result instanceof ResponseProblemInterface) {
    echo \sprintf('Error in request: %s', $result->getReason());
}
