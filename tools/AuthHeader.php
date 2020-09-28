<?php

require __DIR__ . '/../vendor/autoload.php';

$conf = \Uploadcare\Configuration::create(\getenv('UC_PUBLIC'), \getenv('UC_PRIVATE'));
$auth = $conf->getAuthHeaders($argv[1], $argv[2], '');

dump($auth);
