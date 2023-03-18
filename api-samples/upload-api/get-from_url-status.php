<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);
$status = $uploader->checkStatus('945ebb27-1fd6-46c6-a859-b9893712d650');

echo \sprintf('Upload status is %s', $status);
