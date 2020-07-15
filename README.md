# Uploadcare PHP

Uploadcare PHP integration handles uploads by wrapping Upload and REST APIs.

[![Build Status][travis-img]][travis] [![Uploadcare stack on StackShare][stack-img]][stack]  

[travis-img]: https://api.travis-ci.org/uploadcare/uploadcare-php.svg?branch=release-3.0
[travis]: https://travis-ci.org/uploadcare/uploadcare-php
[stack-img]: http://img.shields.io/badge/tech-stack-0690fa.svg?style=flat
[stack]: https://stackshare.io/uploadcare/stacks/

* [Requirements](#requirements)
* [Install](#install)
* [Usage](#usage)
  * [Uploading files](#uploading-files)
  * [File operations](#file-operations)
  * [Tests](#tests)
* [Useful links](#useful-links)

## Requirements

- `php5.6+`
- `php-curl`
- `php-json`

## Install

Prior to installing `uploadcare-php` check if you're using the [Composer](getcomposer.org) dependency manager for PHP. If not, we well recommend you considering it.

**Step 1** — update your `composer.json` with next line:

```js
"require": {
    "uploadcare/uploadcare-php": "^3.0"
}
```

**Step 2** — run [Composer](https://getcomposer.org):

```bash
php composer.phar update
```

**Step 3** — define your Uploadcare public and secret API [keys](https://uploadcare.com/documentation/keys/) in your preferred way — for example, put it to the `$_ENV` variable:

```php
# config.php
$_ENV['UPLOADCARE_PUBLIC_KEY'] = '<your public key>';
$_ENV['UPLOADCARE_PRIVATE_KEY'] = '<your private key>';
```

**Step 4** — include the standard composer autoload file:

```php
require_once 'vendor/autoload.php';
```

**Step 5** — create an object of the `Uploadcare\Configuratoin` class,

```php
$configuration = Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
```

All further operations performed using the configuration object.

## Configuration object

There is a few ways to make valid configuration object. First (and recommended) — use static method of class:

```php
$configuration = \Uploadcare\Configuration::create('<your public key>', '<your private key>');
```

Or you can create the Security signature, Http client and Serializer classes explicitly and then create a configuration:

```php
$sign = new \Uploadcare\Security\Signature('<your private key>', 3600); // Must be an instance of \Uploadcare\Interfaces\SignatureInterface
$client = \Uploadcare\Client\ClientFactory::createClient(); // Must be an instance of \GuzzleHttp\ClientInterface
$serializer = new \Uploadcare\Serializer\Serializer(new \Uploadcare\Serializer\SnackCaseConverter()); // Must be an instance of \Uploadcare\Interfaces\Serializer\SerializerInterface

$configuration = new \Uploadcare\Configuration('<your public key>', $sign, $client, $serializer);
```

As you can see, the fabric method is more convenient for standard usage.

## Usage

You can find all usage examples in `example-project` directory.

### Uploading files

This section describes multiple ways of uploading files to Uploadcare.

For any type of upload, you should create `Uploadcare\Uploader` object with [configuration](#Configuration object)

```php
$configuration = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$uploader = new \Uploadcare\Uploader($configuration);
```

First of, files can be uploaded **from URL**. The following returns an instance of `Uploadcare\File`,

```php
$url = 'https://httpbin.org/image/jpeg';
$result = $uploader->fromUrl($url, 'image/jpeg'); // In success $result will contains uploaded file uuid

echo \sprintf("File from url %s uploaded successfully \n", $url);
echo \sprintf("Uploaded file ID: %s\n", $result);
```

Another way of uploading files is **from a path**,

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromPath($path, 'image/jpeg');  // In success $result will contains uploaded file uuid
```

This will also do when using file pointers,

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromResource(\fopen($path, 'rb'), 'image/jpeg');
```

There's also an option of uploading a file **from its contents**. This will require you to provide MIME-type,

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromContent(\file_get_contents($path), 'image/jpeg');
```

### Multipart upload

If you have a large (more than 100Mb / 10485760 bytes) file, the uploader will automatically process it with [multipart upload](https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart). Note that it will take more than usual time, and we are not recommend do this from web-environment.

## File operations

For any type og file operation you should create the `Uploadcare\FileApi` instance with configuration:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$fileApi = new \Uploadcare\FileApi($config);
```

After that, you can access to file operation methods

- `listFiles($limit = 100, $orderBy = 'datetime_uploaded', $from = null, $addFields = [], $stored = null, $removed = false)` — list of four files. Returns `Uploadcare\Interfaces\Response\FileListResponseInterface`. Each element of collection is a `Uploadcare\Interfaces\File\FileInfoInterface`. Arguments:
    - int             `$limit`     A preferred amount of files in a list for a single response. Defaults to 100, while the maximum is 1000.
    - string          `$orderBy`   specifies the way files are sorted in a returned list
    - string|int|null `$from`      A starting point for filtering files. The value depends on your $orderBy parameter value.
    - array           `$addFields` Add special fields to the file object
    - bool|null       `$stored`    `true` to only include files that were stored, `false` to include temporary ones. The default is unset: both stored and not stored files will be returned.
    - bool            `$removed`   `true` to only include removed files in the response, `false` to include existing files. Defaults to false.

- `storeFile(string $id)` — Store a single file by UUID. Returns `FileInfoInterface`.
    Takes file UUID as argument.
- `deleteFile(string $id)` — Remove individual files. Returns file info.
    Takes file UUID as argument.
- `fileInfo(string $id)` — Once you obtain a list of files, you might want to acquire some file-specific info. Returns `FileInfoInterface`.
    Takes array of file UUID's as argument.
- `batchStoreFile(array $ids)` — Used to store multiple files in one step. Supports up to 100 files per request. Takes array of file UUID's as argument.
- `batchDeleteFile(array $ids)` — Used to delete multiple files in one step. Array of file UUID's as argument 
- `copyToLocalStorage(string $source, bool $store)` — Used to copy original files or their modified versions to default storage. Arguments:
    - `$source` — A CDN URL or just UUID of a file subjected to copy.
    - `$store` — Parameter only applies to the Uploadcare storage
- `copyToRemoteStorage(string $source, string $target, bool $makePublic = true, string $pattern = '${default}')` — copy original files or their modified versions to a custom storage. Arguments:
    - `$source` — CDN URL or just UUID of a file subjected to copy.
    - `$target` — Identifies a custom storage name related to your project. Implies you are copying a file to a specified custom storage. Keep in mind you can have multiple storages associated with a single S3 bucket.
    - `$makePublic` — `true` to make copied files available via public links, `false` to reverse the behavior.
    - `$pattern` — The parameter is used to specify file names Uploadcare passes to a custom storage. In case the parameter is omitted, we use pattern of your custom storage. Use any combination of allowed values.
    
See [original API documentation](https://uploadcare.com/api-refs/rest-api/v0.6.0/#tag/File) for details.

### Tests

PHP 5.6+ tests can be found in the "tests" directory. All tests based on PHPUnit, so you must have it installed on your system to use those.

Tests can be executed using the `vendor/bin/phpunit --exclude-group local-only` command.

`--exclude-group local-only` parameter means that test will not send the real API-requests. If you want to run all tests, you should create the `.env.local` file in `tests` directory and place to this file variables with your real public and private API keys.

```dotenv
# tests/.env.local
UPLOADCARE_PUBLIC_KEY=<your public key>
UPLOADCARE_PRIVATE_KEY=<your private key>
```

## Useful links

[Uploadcare documentation](https://uploadcare.com/docs/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[Upload API reference](https://uploadcare.com/api-refs/upload-api/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[REST API reference](https://uploadcare.com/api-refs/rest-api/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[Changelog](https://github.com/uploadcare/uploadcare-php/blob/master/CHANGELOG.md)  
[Contributing guide](https://github.com/uploadcare/.github/blob/master/CONTRIBUTING.md)  
[Security policy](https://github.com/uploadcare/uploadcare-php/security/policy)  
[Support](https://github.com/uploadcare/.github/blob/master/SUPPORT.md)  
