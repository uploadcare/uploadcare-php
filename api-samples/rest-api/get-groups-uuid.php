<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->group();
$groupInfo = $api->groupInfo('c5bec8c7-d4b6-4921-9e55-6edb027546bc~1');

echo \sprintf("Group: %s files:\n", $groupInfo->getUrl());

foreach ($groupInfo->getFiles() as $file) {
    \sprintf('File: %s (%s)', $file->getUrl(), $file->getUuid());
}
