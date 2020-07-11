<?php

require_once \dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Uploadcare\Configuration;
use Uploadcare\Uploader;

(new Dotenv())->loadEnv(__DIR__ . '/.env');

$configuration = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$uploader = new Uploader($configuration);

$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromPath($path, 'image/jpeg');

echo \sprintf("File %s uploaded successfully \n", \realpath($path));
dump($result);
