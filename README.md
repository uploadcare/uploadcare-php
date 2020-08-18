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

As you can see, the factory method is more convenient for standard usage.

## Usage

You can find full usage example in [Uploadcare example project](https://github.com/uploadcare/uploadcare-php-example)

For all types of actions, you should create the Api object first:

```php
$configuration = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$api = new \Uploadcare\Api($configuration);
```

### Uploading files

This section describes multiple ways of uploading files to Uploadcare.

You can use the core API-object for any type of upload:

```php
$configuration = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$uploader = (new \Uploadcare\Api($configuration))->uploader();
```

First of, files can be uploaded **from URL**. The following returns an instance of `Uploadcare\File`,

```php
$url = 'https://httpbin.org/image/jpeg';
$result = $uploader->fromUrl($url, 'image/jpeg'); // In success $result will contains instance of Uploadcare\File class (see below)

echo \sprintf("File from url %s uploaded successfully \n", $url);
echo \sprintf("Uploaded file ID: %s\n", $result);
```

Another way of uploading files is **from a path**,

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromPath($path, 'image/jpeg');  // In success $result will contains uploaded Uploadcare\File class
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

#### `Uploadcare\File` class

This class implements `Uploadcare\Interfaces\File\FileInfoInterface` and has additional (besides the interface) methods:

- `store()` — store this file in storage. Returns `Uploadcare\File` object;
- `delete()` — delete this file. Returns `Uploadcare\File` object;
- `copyToLocalStorage($store = true)` — copy this file to local storage;
- `copyToRemoteStorage($target, $makePublic = true, $pattern = null)` — copy this file to remote storage;

All those operations are accessible through File API, but through `Uploadcare\File` object too.

## File operations

For any type of file operation you should create the `Uploadcare\Api` instance with configuration object and call the `file()` method:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$fileApi = (new \Uploadcare\Api($config))->file();
```

After that, you can access to file operation methods

- `listFiles($limit = 100, $orderBy = 'datetime_uploaded', $from = null, $addFields = [], $stored = null, $removed = false)` — list of four files. Returns `Uploadcare\FileCollection` instance (see below). Each element of collection is a `Uploadcare\File`. Arguments:
    - int             `$limit`     A preferred amount of files in a list for a single response. Defaults to 100, while the maximum is 1000.
    - string          `$orderBy`   specifies the way to sort files in a returned list
    - string|int|null `$from`      A starting point for filtering files. The value depends on your $orderBy parameter value.
    - array           `$addFields` Add special fields to the file object
    - bool|null       `$stored`    `true` to only include files that were stored, `false` to include temporary ones. The default is unset: both stored and not stored files will be returned.
    - bool            `$removed`   `true` to only include removed files in the response, `false` to include existing files. Defaults is false.
- `nextPage(FileListResponseInterface $response)` — next page from previous answer, if next page are exists. You can use it in simple `while` loop, for example:     
    ```php
    $config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
    $fileApi = new \Uploadcare\Apis\FileApi($config);
    $page = $fileApi->listFiles(5); // Here is a FileListResponseInterface
    while (($page = $fileApi->nextPage($page)) !== null) {
      $files = $page->getResults(); 
    }
    ```
- `storeFile(string $id)` — Store a single file by UUID. Returns the `Uploadcare\File` (`FileInfoInterface`).
    Takes file UUID as argument.
- `deleteFile(string $id)` — Remove individual files. Returns file info.
    Takes file UUID as argument.
- `fileInfo(string $id)` — Once you obtain a list of files, you might want to acquire some file-specific info. Returns `FileInfoInterface`.
    Takes array of file UUID's as argument.
- `batchStoreFile(array $ids)` — Used to store multiple files in one step. Supports up to 100 files per request. Takes array of file UUID's as argument.
- `batchDeleteFile(array $ids)` — Used to delete multiple files in one step. Array of file UUID's as argument 
- `copyToLocalStorage(string $source, bool $store)` — Used to copy original files, or their modified versions to default storage. Arguments:
    - `$source` — A CDN URL or just UUID of a file subjected to copy.
    - `$store` — Parameter only applies to the Uploadcare storage
- `copyToRemoteStorage(string $source, string $target, bool $makePublic = true, string $pattern = '${default}')` — copy original files, or their modified versions to a custom storage. Arguments:
    - `$source` — CDN URL or just UUID of a file subjected to copy.
    - `$target` — Identifies a custom storage name related to your project. Implies you are copying a file to a specified custom storage. Keep in mind you can have multiple storages associated with a single S3 bucket.
    - `$makePublic` — `true` to make copied files available via public links, `false` to reverse the behavior.
    - `$pattern` — The parameter is used to specify file names Uploadcare passes to a custom storage. In case the parameter is omitted, we use pattern of your custom storage. Use any combination of allowed values.
    
See [original API documentation](https://uploadcare.com/api-refs/rest-api/v0.6.0/#tag/File) for details.

### `Uploadcare\FileCollection` class

This class implements `Uploadcare\Interfaces\File\CollectionInterface` and has additional (besides the interface) methods:

- `store()` — sores all files in collection. Calls `FileApi::batchStoreFile()` under the hood;
- `delete()` — deletes all files in collection.

Each file in collection is an object of `Uploadcare\File` class.

### `Uploadcare\File` class

This class implements `FileInfoInterface` and has additional methods for file operations:
- `store()` — stores the current file;
- `delete()` — deletes the current file;
- `copyToLocalStorage($store = true)` — copies the current file to default storage;
- `copyToRemoteStorage($target, $makePublic = true, $pattern = null)` — copies the current file to custom storage;

As you can see, additional methods helps you to call API methods without direct API calls.

## Group operations

For any type of group operation you should create the `Uploadcare\Api` instance with configuration object and call the `group()` method:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$groupApi = (new \Uploadcare\Api($config))->group();
```

After that, you can access to group operation methods

- `createGroup($files)` — Creates file group. You can pass the array of IDs or `Uploadcare\File\FileCollection` as argument. Returns `Uploadcare\File\Group` object.
- `listGroups($limit, $asc = true)` — Get a paginated list of groups. Default limit is 100, default order is order by creation datetime. You can reverse order with `$asc = false`. Returns `Uploadcare\Response\GroupListResponse`
- `groupInfo($id)` — Gets a file group info by UUID. Returns `Uploadcare\Group` object.
- `storeGroup($id)` — Mark all files in a group as stored. Returns `Uploadcare\Group` object.

### `Uploadcare\Group` class

This class implements `Uploadcare\Interfaces\GroupInterface` and has additional method `store()` — applies the store operation for the group. Calls `GroupApi::store group`;

The `getFiles()` method of the `Uploadcare\Group` object returns [FileCollection](#uploadcarefilecollection-class).

## Project operations

As usual, Project API accessible by main API object

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$projectApi = (new \Uploadcare\Api($config))->project();
```

You can get the project information by call `getProjectInfo` method:

```php
$projectInfo = $projectApi->getProjectInfo();
```

Now, `$projectInfo` variable contains `Uploadcare\Interfaces\Response\ProjectInfoInterface` implementation with next methods:

- `getCollaborators()` — array with collaborators information. Keys are Collaborator emails and values are Collaborator names;
- `getName()` — project name as string;
- `getPubKey()` — project public key as string;
- `isAutostoreEnabled()` — is project files stores automatically;

## Webhooks operations

Call the webhook API:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$webhookApi = (new \Uploadcare\Api($config))->webhook();
```

And methods are:

- `listWebhooks()` — returns list of project webhooks as instance of `Uploadcare\WebhookCollection` class. Each element of this collection is instance of `Uploadcare\Webhook` class (see below);
- `createWebhook($targetUrl, $isActive = true, $event = 'file.uploaded')` — creates new webhook for the event. Returns `Uploadcare\Webhook` class.
- `updateWebhook($id, array $parameters)` — updates an existing webhook with parameters. Parameters can be:
    - `target_url` — Target callback url;
    - `event` — only `file.uploaded` event are valid and supported right now;
    - `is_active` — webhook activity status;
- `deleteWebhook` — deletes webhook by url;

#### `Uploadcare\Webhook` class

This class implements `Uploadcare\Interfaces\Response\WebhookInterface` and has additional (besides the interface) methods:

- `delete()` — deletes webhook. Calls `Uploadcare\Interfaces\Api\WebhookApiInterface::deleteWebhook()` under the hood;
- `updateUrl($url)` — updates webhook with new URL (`WebhookApiInterface::updateWebhook()`);
- `activate()` — sets webhook active (`WebhookApiInterface::updateWebhook()`);
- `deactivate()` — sets webhook not active (`WebhookApiInterface::updateWebhook()`);

## Conversion operations

You can convert documents, images and video files with Conversion API. 

### Documents and images conversion

You should make object for conversion request:

```php
$request = new \Uploadcare\Conversion\DocumentConversionRequest('pdf');
```

Default arguments is:
- `$targetFormat = 'pdf'` — target format
- `$throwError = false` — if set to `true`, wrong request will throw exception, otherwise, return null;
- `$store = true` — whether conversion result will store in default storage;
- `$pageNumber = 1` — in case you convert multi-page (PDF, for example) document to image, this parameter defines the source document page to conversion;

After that, you can covert your file:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$convertor = (new \Uploadcare\Api($config))->conversion();
$result = $convertor->convertDocument($file, $request);
```

Result will contain one of two objects:
- `ConvertedItemInterface` object with conversion result in case of successful operation, or
- `ResponseProblemInterface` object in case of error (and `$throwError` in request sets to `false`).

The `ConvertedItemInterface` will contains UUID of converted document and token with conversion job ID. You can request the conversion job status with this ID (or `ConvertedItemInterface` object itself):

```php
$status = $convertor->documentJobStatus($result); // or $result->getToken()
```

This status object will implement `ConversionStatusInterface` with methods:
- `getStatus()` — status string. Pending, processing, finished, failed or canceled;
- `getError()` — string in case of error or null;
- `getResult()` — `StatusResultInterface` object.

You can request conversion for several documents:

```php
$result = $convertor->batchConvertDocuments($files, $request);
```

`$files` can be the array / collection of file IDs or FileInfo objects and result will be the implementation of `BatchResponseInterface`.

### Video conversion

Get the conversion API as in previous step and make `VideoEncodingRequest`

```php
$request = new \Uploadcare\Conversion\VideoEncodingRequest();
```

You can set various parameters for this request trough object setters:

```php
$request = (new \Uploadcare\Conversion\VideoEncodingRequest())
    ->setHorizontalSize(1024)           // Sets the horizontal size for result.
    ->setVerticalSize(768)              // Sets the vertical size of result.
    ->setResizeMode('preserve_ratio')   // Sets the resize mode. Mode can be one of 'preserve_ratio', 'change_ratio', 'scale_crop', 'add_padding'
    ->setQuality('normal')              // Sets result quality. Can be one of 'normal', 'better', 'best', 'lighter', 'lightest'
    ->setTargetFormat('mp4')            // Sets the target format. Can be one of 'webm', 'ogg', 'mp4'. Default 'mp4'
    ->setStartTime('0:0')               // Sets the start time. Time string must be an `HHH:MM:SS.sss` or `MM:SS.sss`
    ->setEndTime('22:44')               // Sets the end time. String format like start time string
    ->setThumbs(2)                      // Sets count of video thumbs. Default 1, max 50
    ->setStore(true)                    // Tells store result at conversion ends. Default is true
```

If you not set any option to conversion request, the defaults will be mp4 format, full length and normal quality.

As a result of Conversion API `convertVideo` method you will get the `ConversionResult` or `ResponseProblem` object. `ConversionResult` object will contain the `uuid` and `token`. You can use token to request status of video conversion job with `videoJobStatus` method.

Also you can request a batch video conversion with `batchConvertVideo` method. First argument must be a collection of FileInfo or file uuid's, second — `VideoEncodingRequest` object.

--------------------------------------------------------------------

## Tests

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
