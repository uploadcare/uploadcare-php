<?php
/**
 * This example shows how to download all files from Uploadcare storage
 * to your local disk
 */

// This is just some config with public and secret keys for UC.
require_once 'config.php';
// requesting autoloader that got uploadcare in there
require_once '../vendor/autoload.php';
// using api
use Uploadcare\Api;

// create object instance for Api.
$api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

// get all files iterator
$files = $api->getFileList();

// folder to store files in
$folder = 'download';

// creating a folder for downloaded files
if (!file_exists($folder)) {
  mkdir($folder);
}
chmod($folder, 0775);

/** @var \Uploadcare\File $file */
foreach ($files as $file) {
  $originalFilename = $file->data['original_filename'];

  // if you see an error on this line like this: Unable to find the wrapper "https" - did you forget to enable it when you configured PHP?
  // then you should enable openssl extension in php
  // more info here: http://ru.stackoverflow.com/questions/222688/denwer-�-file-get-contents
  file_put_contents($folder . '/' . $originalFilename, fopen($file, 'r'));

  echo "downloaded {$originalFilename} (" . filesize($folder . '/' . $originalFilename) . " bytes)<br>";
}