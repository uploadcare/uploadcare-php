<?php
// This is just some config with public and secret keys for UC.
require_once 'config.php';
// requesting autoloader that got uploadcare in there
require_once 'vendor/autoload.php';
// using api
use Uploadcare\Api;

// create object instance for Api.
$api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

// get all files iterator
$files = $api->getFileList();

// get all files iterator
$files = $api->getFileList();

/** @var \Uploadcare\File $file */
foreach ($files as $file) {
  $originalFilename = $file->data['original_filename'];

  $file->delete();

  echo "{$originalFilename} (" . $file . ") removed<br>";
}