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

### Tests

PHP 5.6+ tests can be found in the "tests" directory. The tests are based on PHPUnit, so you must have it installed on your system to use those.

Tests can be executed using the `vendor/bin/phpunit` command.

## Useful links

[Uploadcare documentation](https://uploadcare.com/docs/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[Upload API reference](https://uploadcare.com/api-refs/upload-api/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[REST API reference](https://uploadcare.com/api-refs/rest-api/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[Changelog](https://github.com/uploadcare/uploadcare-php/blob/master/CHANGELOG.md)  
[Contributing guide](https://github.com/uploadcare/.github/blob/master/CONTRIBUTING.md)  
[Security policy](https://github.com/uploadcare/uploadcare-php/security/policy)  
[Support](https://github.com/uploadcare/.github/blob/master/SUPPORT.md)  
