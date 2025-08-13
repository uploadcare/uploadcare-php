<?php

use Uploadcare\Interfaces\File\FileInfoInterface;

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->file();

$result = $api->batchStoreFile(['b7a301d1-1bd0-473d-8d32-708dd55addc0', '1bac376c-aa7e-4356-861b-dd2657b5bfd2']);

foreach ($result->getResult() as $result) {
    if (!$result instanceof FileInfoInterface) {
        continue;
    }

    \printf('Result %s is stored at %s', $result->getUuid(), $result->getDatetimeStored()->format(DateTimeInterface::ATOM));
}
