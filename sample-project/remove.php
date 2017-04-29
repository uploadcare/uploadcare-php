<?php
// This is just some config with public and secret keys for UC.
require_once 'config.php';
// requesting autoloader that got uploadcare in there
require_once '../vendor/autoload.php';
// using api
use Uploadcare\Api;

// create object instance for Api.
$api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

// get all files iterator
// using filter: only files uploaded earlier than 14 days ago
$files = $api->getFileList(array(
  'to' => new \DateTime('-14 days')
));

/** @var \Uploadcare\File $file */
foreach ($files as $file) {
  $originalFilename = $file->data['original_filename'];

  $file->delete();

  echo "{$originalFilename} (" . $file . ") removed<br>";
}