<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);
$groupInfo = $uploader->groupInfo('0d712319-b970-4602-850c-bae1ced521a6~1');

echo $groupInfo->getBody()->getContents();
