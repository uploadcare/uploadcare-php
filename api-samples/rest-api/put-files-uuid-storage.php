<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->file();
$result = $api->storeFile('1bac376c-aa7e-4356-861b-dd2657b5bfd2');

echo \sprintf('File %s is stored at %s', $result->getUuid(), $result->getDatetimeStored()->format(\DateTimeInterface::ATOM));
