<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$uploader = new Uploadcare\Uploader\Uploader($configuration);

$result = $uploader->groupFiles(['d6d34fa9-addd-472c-868d-2e5c105f9fcd', 'b1026315-8116-4632-8364-607e64fca723/-/resize/x800/']);

echo \sprintf('Response status is %s', $result->getStatusCode());
