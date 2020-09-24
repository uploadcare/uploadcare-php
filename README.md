# Uploadcare PHP

Uploadcare PHP integration handles uploads and further operations with files by wrapping Upload and REST APIs.

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
  * [Group operations](#group-operations)
  * [Project operations](#project-operations)
  * [Webhooks](#webhooks-operations)
  * [Conversion operations](#conversion-operations)
  * [Secure delivery](#secure-delivery)
  * [Tests](#tests)
* [Useful links](#useful-links)

## Requirements

- `php5.6+`
- `php-curl`
- `php-json`

## Install

Prior to installing `uploadcare-php` get the [Composer](getcomposer.org) dependency manager for PHP because it'll simplify installation.

**Step 1** — update your `composer.json`:

```js
"require": {
    "uploadcare/uploadcare-php": "^3.0"
}
```

**Step 2** — run [Composer](https://getcomposer.org):

```bash
php composer.phar update
```

**Step 3** — define your Uploadcare public and secret API [keys](https://uploadcare.com/documentation/keys/) in a way you prefer (e.g., by using a `$_ENV` variable):

```php
# config.php
$_ENV['UPLOADCARE_PUBLIC_KEY'] = '<your public key>';
$_ENV['UPLOADCARE_PRIVATE_KEY'] = '<your private key>';
```

**Step 4** — include a standard composer autoload file:

```php
require_once 'vendor/autoload.php';
```

**Step 5** — create an object of the `Uploadcare\Configuratoin` class,

```php
$configuration = Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
```

All further operations will use this configuration object.

## Configuration object

There are a few ways to make a valid configuration object. We recommend using the static method of the class:

```php
$configuration = \Uploadcare\Configuration::create('<your public key>', '<your private key>');
```

Alternatively, you can create a Security signature, HTTP client, and Serializer classes explicitly. Then create a configuration:

```php
$sign = new \Uploadcare\Security\Signature('<your private key>', 3600); // Must be an instance of \Uploadcare\Interfaces\SignatureInterface
$client = \Uploadcare\Client\ClientFactory::createClient(); // Must be an instance of \GuzzleHttp\ClientInterface
$serializer = new \Uploadcare\Serializer\Serializer(new \Uploadcare\Serializer\SnackCaseConverter()); // Must be an instance of \Uploadcare\Interfaces\Serializer\SerializerInterface

$configuration = new \Uploadcare\Configuration('<your public key>', $sign, $client, $serializer);
```

As you can see, the factory method is more convenient for standard usage.

## Usage

You can find the full example in this [Uploadcare example project](https://github.com/uploadcare/uploadcare-php-example).

First, create an API object:

```php
$configuration = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$api = new \Uploadcare\Api($configuration);
```

### Uploading files

This section describes multiple ways of uploading files to Uploadcare.

You can use the core API object for any upload type:

```php
$configuration = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$uploader = (new \Uploadcare\Api($configuration))->uploader();
```

First of all, files can be uploaded **from URL**. The following code returns an instance of `Uploadcare\File`,

```php
$url = 'https://httpbin.org/image/jpeg';
$result = $uploader->fromUrl($url, 'image/jpeg'); // If success, $result will contain instance of Uploadcare\File class (see below)

echo \sprintf("File from URL %s uploaded successfully \n", $url);
echo \sprintf("Uploaded file ID: %s\n", $result);
```

Another way of uploading files is by using **a path**,

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromPath($path, 'image/jpeg');  // In success $result will contains uploaded Uploadcare\File class
```

It’s also relevant when using file pointers,

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromResource(\fopen($path, 'rb'), 'image/jpeg');
```

There's an option of uploading a file **from its contents**. You’ll need to specify MIME-types as well:

```php
$path = __DIR__ . '/squirrel.jpg';
$result = $uploader->fromContent(\file_get_contents($path), 'image/jpeg');
```

### Multipart upload

If you have a large file (more than 100Mb / 10485760 bytes), the uploader will automatically process it with a [multipart upload](https://uploadcare.com/api-refs/upload-api/#operation/multipartFileUploadStart). It'll take more time to upload, and also we don’t recommend it for a web environment.

#### `Uploadcare\File` class

This class implements `Uploadcare\Interfaces\File\FileInfoInterface` and it has additional methods (besides the interface):

- `store()` — Stores this file in storage. Returns `Uploadcare\File` object.
- `delete()` — Deletes this file. Returns `Uploadcare\File` object.
- `copyToLocalStorage($store = true)` — Copies this file to local storage.
- `copyToRemoteStorage($target, $makePublic = true, $pattern = null)` — Copies this file to remote storage.

All these operations are accessible via File API, and you can access them through the `Uploadcare\File` object as well.

## File operations

For any file operation type, you’ll need to create an `Uploadcare\Api`  instance with configuration object and call the `file()` method:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$fileApi = (new \Uploadcare\Api($config))->file();
```

After that, you can access to file operation methods:

- `listFiles($limit = 100, $orderBy = 'datetime_uploaded', $from = null, $addFields = [], $stored = null, $removed = false)` — a list of files. Returns an `Uploadcare\FileCollection` instance (see below). Each element of collection is an `Uploadcare\File`. Arguments:
    - int             `$limit`     A preferred amount of files in a list for a single response. Defaults to 100, while the maximum is 1000.
    - string          `$orderBy`   Specifies the way to sort files in a returned list.
    - string|int|null `$from`      A starting point for a file filter. The value depends on your `$orderBy` parameter value.
    - array           `$addFields` Adds special fields to the file object.
    - bool|null       `$stored`    `true` includes the only stored files, `false` includes temporary files. If not set (default): both stored and not stored files will be included.
    - bool            `$removed`   `true` to only include removed files in the response, `false` to include existing files. The default value is false.
- `nextPage(FileListResponseInterface $response)` — next page from previous answer, if next pages exist. You can use it in a simple `while` loop, for example:     
    ```php
    $config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
    $fileApi = new \Uploadcare\Apis\FileApi($config);
    $page = $fileApi->listFiles(5); // Here is a FileListResponseInterface
    while (($page = $fileApi->nextPage($page)) !== null) {
      $files = $page->getResults(); 
    }
    ```
- `storeFile(string $id)` — Stores a single file by UUID. Returns the `Uploadcare\File` (`FileInfoInterface`).
    Takes file UUID as an argument.
- `deleteFile(string $id)` — Removes individual files. Returns file info.
    Takes file UUID as an argument.
- `fileInfo(string $id)` — Once you obtain a list of files, you might want to acquire some file-specific info. Returns `FileInfoInterface`.
    Takes array of file UUID's as an argument.
- `batchStoreFile(array $ids)` — Used to store multiple files in one step. Supports up to 100 files per request. Takes an array of file UUID's as an argument.
- `batchDeleteFile(array $ids)` — Used to delete multiple files in one step. Takes an array of file UUID's as an argument.
- `copyToLocalStorage(string $source, bool $store)` — Used to copy original files, or their modified versions to a default storage. Arguments:
    - `$source` — A CDN URL or just UUID of a file subjected to copy.
    - `$store` Parameter only applies to the Uploadcare storage.
- `copyToRemoteStorage(string $source, string $target, bool $makePublic = true, string $pattern = '${default}')` — copies original files, or their modified versions to a custom storage. Arguments:
    - `$source` — CDN URL or just UUID of a file that’s being copied.
    - `$target` — Defines a custom storage name related to your project and implies you are copying a file to a specified custom storage. Keep in mind that you can have multiple storages associated with one S3 bucket.
    - `$makePublic` — `true` to make copied files available via public links, `false` to reverse the behavior.
    - `$pattern` — The parameter is used to specify file names Uploadcare passes to a custom storage. In case when the parameter is omitted, we use a pattern of your custom storage. Use any combination of allowed values.
    
See the [API documentation](https://uploadcare.com/api-refs/rest-api/v0.6.0/#tag/File) for more details.

### `Uploadcare\FileCollection` class

This class implements `Uploadcare\Interfaces\File\CollectionInterface` and has additional methods besides the interface:

- `store()` — Stores all files in a collection. Calls `FileApi::batchStoreFile()` under the hood.
- `delete()` — Deletes all files in a collection.

Each file in the collection is an object of the `Uploadcare\File` class.

### `Uploadcare\File` class

This class implements `FileInfoInterface` and has additional methods for file operations:
- `store()` — Stores the current file.
- `delete()` — Deletes the current file.
- `copyToLocalStorage($store = true)` — Copies the current file to the default storage.
- `copyToRemoteStorage($target, $makePublic = true, $pattern = null)` — Copies the current file to a custom storage;

As you can see, additional methods help you to call API methods without direct API calls.

## Group operations

For any type of group operation you need to create an `Uploadcare\Api` instance with a configuration object and call the `group()` method:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$groupApi = (new \Uploadcare\Api($config))->group();
```

After that, you can access group operation methods:

- `createGroup($files)` — Creates a file group. You can pass the array of IDs or `Uploadcare\File\FileCollection` as an argument. Returns an `Uploadcare\File\Group` object.
- `listGroups($limit, $asc = true)` — Gets a paginated list of groups. The default limit is 100, and the default sorting is by the date and time created (ascending). You can reverse the sorting order to descending dates with `$asc = false`. Returns `Uploadcare\Response\GroupListResponse`.
- `groupInfo($id)` — Gets a file group info by UUID. Returns an `Uploadcare\Group` object.
- `storeGroup($id)` — Marks all files in a group as stored. Returns an `Uploadcare\Group` object.

### `Uploadcare\Group` class

This class implements `Uploadcare\Interfaces\GroupInterface` aand has an additional `store()` method that applies the store operation to the group. Calls `GroupApi::store group`;

The `getFiles()` method of the `Uploadcare\Group` object returns [FileCollection](#uploadcarefilecollection-class).

## Project operations

As usual, Project API is accessible by the main API object:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$projectApi = (new \Uploadcare\Api($config))->project();
```

You can get the project information by calling the `getProjectInfo` method:

```php
$projectInfo = $projectApi->getProjectInfo();
```

Now, the `$projectInfo` variable contains the `Uploadcare\Interfaces\Response\ProjectInfoInterface` implementation with the following methods:

- `getCollaborators()` — Array with collaborators information. Keys are collaborator emails and values are collaborator names.
- `getName()` — Project name as a string.
- `getPubKey()` — Project public key as string.
- `isAutostoreEnabled()` — Returns `true` if the project files are stored automatically.

## Webhooks

Call the webhook API:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$webhookApi = (new \Uploadcare\Api($config))->webhook();
```

The methods are:

- `listWebhooks()` — Returns a list of project webhooks as an instance of an `Uploadcare\WebhookCollection` class. Each element of this collection is an instance of a `Uploadcare\Webhook` class (see below);
- `createWebhook($targetUrl, $isActive = true, $event = 'file.uploaded')` — Creates a new webhook for the event. Returns the `Uploadcare\Webhook` class.
- `updateWebhook($id, array $parameters)` — Updates an existing webhook with these parameters. Parameters can be:
    - `target_url` — A target callback URL;
    - `event` — The only `file.uploaded` event is supported at the moment.
    - `is_active` — Returns the webhook activity status.
- `deleteWebhook` — Deletes a webhook by URL.

#### `Uploadcare\Webhook` class

This class implements `Uploadcare\Interfaces\Response\WebhookInterface` and has additional methods besides the interface:

- `delete()` — Deletes a webhook. Calls `Uploadcare\Interfaces\Api\WebhookApiInterface::deleteWebhook()` under the hood;
- `updateUrl($url)` — Updates a webhook with a new URL (`WebhookApiInterface::updateWebhook()`).
- `activate()` — Sets a webhook active (`WebhookApiInterface::updateWebhook()`).
- `deactivate()` — Sets a webhook as not active (`WebhookApiInterface::updateWebhook()`).

## Conversion operations

You can convert documents, images and encode video files with Conversion API.

### Document and image conversion

Create a new object for a subsequent conversion request:

```php
$request = new \Uploadcare\Conversion\DocumentConversionRequest('pdf');
```

The default arguments and examples are:

- `$targetFormat = 'pdf'` — Target format.
- `$throwError = false` — If set to `true`, a wrong request will throw an exception, otherwise, it'll return null.
- `$store = true` —  The conversion results will go to your default storage.
- `$pageNumber = 1` — Specifies pages to convert in multi-page documents.

After that, you can covert your file:

```php
$config = \Uploadcare\Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
$convertor = (new \Uploadcare\Api($config))->conversion();
$result = $convertor->convertDocument($file, $request);
```

Result will contain one of two objects:
- `ConvertedItemInterface` object with conversion result in case of successful operation, or
- `ResponseProblemInterface` object in case of error (and `$throwError` in request sets to `false`).

The `ConvertedItemInterface` will contain a UUID of converted document and token with conversion job ID. You can request the conversion job status with this ID (or the `ConvertedItemInterface` object itself):

```php
$status = $convertor->documentJobStatus($result); // or $result->getToken()
```

This status object will implement `ConversionStatusInterface` with these methods:
- `getStatus()` — Status string. Pending, processing, finished, failed or canceled.
- `getError()` — An error string in case of error or null.
- `getResult()` — The `StatusResultInterface` object.

You can request batch conversion to process multiple documents:

```php
$result = $convertor->batchConvertDocuments($files, $request);
```

`$files` can be an array / collection of file IDs or FileInfo objects and the result will be the implementation of `BatchResponseInterface`.

### Video encoding

Get the conversion API as in the previous step and perform `VideoEncodingRequest`

```php
$request = new \Uploadcare\Conversion\VideoEncodingRequest();
```

You can set various parameters for this request through the object setters:

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

If you don’t set any option to conversion request, the defaults will be as follows: mp4 format, full length and normal quality.

As a result of the Conversion API `convertVideo` method, you will get the `ConversionResult` or `ResponseProblemobject`. ConversionResult object that contains the `uuid` and `token`. You can use a token to request the status of a video conversion job with `videoJobStatus` method.

Also, you can request a batch video conversion with `batchConvertVideo` method. The first argument must be a collection of FileInfo or file uuid's, and the second — `VideoEncodingRequest` object.

## Secure delivery

You can use your own custom domain and CDN provider for deliver files with authenticated URLs (see [original documentation](https://uploadcare.com/docs/security/secure_delivery/)).

To generate authenticated URL from the library, you should do this:

- make a configuration as usual;
- make `Uploadcare\AuthUrl\AuthUrlConfig` object. This object will provide token, expire timestamp and your custom domain URL generator. `$token` in the constructor must be an instance of `Uploadcare\AuthUrl\Token\TokenInterface`;
- generate secure url from `FileApi::generateSecureUrl($id)` or from `Uploadcare\File::generateSecureUrl()`

For example:

```php
use Uploadcare\Configuration;
use Uploadcare\AuthUrl\AuthUrlConfig;
use Uploadcare\Api;
use Uploadcare\AuthUrl\Token\AkamaiToken;

$authUrlConfig = new AuthUrlConfig('mydomain.com', new AkamaiToken('secretKey', 300));
$config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY'])
    ->setAuthUrlConfig($authUrlConfig);

$api = new Api($config);
$file = $api->file()->listFiles()->getResults()->first();

// Get secure url from file
$secureUrl = $file->generateSecureUrl(); // you can use KeyCdnUrlGenerator or AkamaiUrlGenerator

// Or from API instance
$secureUrlFromApi = $api->file()->generateSecureUrl($file);
```

--------------------------------------------------------------------

## Tests

PHP 5.6+ tests can be found in the "tests" directory. All tests are based on PHPUnit, so you need to have it installed prior to running tests.

Run tests with this command:

```bash
`vendor/bin/phpunit --exclude-group local-only`
```

`--exclude-group local-only` means that a test will not send real API-requests. If you want to run all tests, create the `.env.local` file in the `tests` directory and place the following variables with your real public and private API keys:

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
