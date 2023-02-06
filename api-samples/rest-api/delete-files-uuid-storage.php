<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$fileInfo = (new Uploadcare\Api($configuration))->file()->deleteFile('1bac376c-aa7e-4356-861b-dd2657b5bfd2');
echo \sprintf('File \'%s\' deleted at \'%s\'', $fileInfo->getUuid(), $fileInfo->getDatetimeRemoved()->format(\DateTimeInterface::ATOM));
