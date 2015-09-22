# Uploadcare PHP

This is a set of libraries to work with [Uploadcare][1].

[![Build Status](https://travis-ci.org/uploadcare/uploadcare-php.png?branch=master)](https://travis-ci.org/uploadcare/uploadcare-php)

## Requirements

- `php5.3+`
- `php-curl`
- `php-json`

## Install

Just update your `composer.json` with:

```js
    "require": {
        "uploadcare/uploadcare-php": ">=v1.1.0,<2.0"
    }
```

and run [Composer](https://getcomposer.org):

```bash
php composer.phar update
```

If you like, define some constants with Public and Secret keys within your project:

```php
define('UC_PUBLIC_KEY', 'demopublickey');
define('UC_SECRET_KEY', 'demoprivatekey');
```

Just include one file to start using Uploadcare inside your PHP project and use namespace `\Uploadcare`:

```php
require_once 'vendor/autoload.php';
use \Uploadcare;
```

Now, we are ready. Create an object of Uploadcare\Api class:

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
```

This is a main object your should work with. It has everything you need.

## Widgets and simple example

Let's start with widgets.

If you want to get Javascript's url for widget, just call:

```php
print $api->widget->getScriptSrc()
```

You can easily get all contents and &lt;script&gt; sections to include in your HTML:

```php
    <head>
    <?php print $api->widget->getScriptTag(); ?>
    </head>
```

Create some form to use with widget:

```php
<form method="POST" action="upload.php">
  <?php echo $api->widget->getInputTag('qs-file'); ?>
  <input type="submit" value="Save!" />
 </form>
```

You will see an Uploadcare widget. After selecting file the "file_id" parameter will be set as value of hidden field.

The last thing left is to store file:

```php
$file_id = $_POST['qs-file'];
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
$file = $api->getFile($file_id);
$file->store();
```

Now you have an Uploadcare\File object to work with. You can show an image like this:

```php
<img src="<?php echo $file->getUrl(); ?>" />
```

Or just:

```php
<img src="<?php echo $file; ?>" />
```

Or you can even call a getImgTag method. This will return a prepared <img> tag:

```php
echo $file->getImgTag('image.jpg', array('alt' => 'Image'));
```

## API and requests

You can do any simple request if you like by calling:

```php
$api->request($method, $path, $data = array(), $headers = array());
```

Don't forget, that each API url has it's own allowed methods.

If method is not allowed exceptions will be thrown.

Ok, lets do some requests. This is request to index (https://api.uploadcare.com).

This will return an stdClass with information about urls you can request.

This is not really valuable data.

```php
$data = $api->request('GET', '/');
```

Lets request account info.

This will return just some essential data inside stdClass such as: username, pub_key and email

```php
$account_data = $api->request('GET', '/account/');
```

Now lets get file list.

This request will return stdClass with all files uploaded and some information about files.

Each files has:

- size
- upload_date
- last_keep_claim
- on_s3
- made_public
- url
- is_image
- uuid
- original_filename
- removed
- mime_type
- original_file_url


```php
$files_raw = $api->request('GET', '/files/');
```

Previous request is just some raw request and it will return raw data from json.

There's a better way to handle all the files by using method below.

It will return an array of \Uploadcare\File objects to work with.

This objects provide ways to display the file and to use methods such as resize, crop, etc

```php
$files = $api->getFileList();
```

getFileList called without any params will return just an array of first 20 files objects (first page).

But you can supply a page you want to see:

```php
$page = 2;
$files = $api->getFileList($page);
```

You can get some information about pagination.

You will get an array with params:

- page: current page
- next: uri to request next page
- per_page: number of files per page
- pages: number of pages
- previous: uri to request previous page

Use "per_page" and "pages" information to create pagination inside your own project

```php
$pagination_info = $api->getFilePaginationInfo();
```

If you have a file's UUID or CDN URL (for example, it's saved in your database) you can create object for file easily:

```php
$uuid = '3c99da1d-ef05-4d79-81d8-d4f208d98beb';
$file1 = $api->getFile($uuid);

$cdnurl = 'https://ucarecdn.com/3c99da1d-ef05-4d79-81d8-d4f208d98beb/-/preview/100x100/-/effect/grayscale/bill.jpg';
$file2 = $api->getFile($cdnurl);
```

You can access raw data like this:

```php
$file->data['size'];
```

Trying to access "data" parameter will fire GET request to get all that data once.
It will be a cached array if you will try to access "data" parameter again.

## Groups

To get list of groups:

```php
$from = '2013-10-10';
$api->getGroupList($from);
```

"$from" parameter is not required. You will get an array of Group objects.

To get group:

```php
$group_id = 'badfc9f7-f88f-4921-9cc0-22e2c08aa2da~12';
$group = $api->getGroup($group_id);
```

To retrieve files for group:

```php  
$files = $group->getFiles();
```

To store group:

```php
$group->store();
```

## File operations

Using object of \Uploadcare\File class we can get url for the file

```php
echo $file->getUrl();
```

Now let's do some crop.

```php
$width = 400;
$height = 400;
$is_center = true;
$fill_color = 'ff0000';
echo $file->crop($width, $height, $is_center, $fill_color)->getUrl();
```

And here's some resize with width and height

```php
echo $file->resize($width, $height)->getUrl();
```

Width only

```php
echo $file->resize($width)->getUrl();
```

Height only

```php
echo $file->resize(false, $height)->getUrl();
```

We can also use scale crop

```php
echo $file->scaleCrop($width, $height, $is_center)->getUrl();
```

And we can apply some effects.

```php
echo $file->effect('flip')->getUrl();
echo $file->effect('grayscale')->getUrl();
echo $file->effect('invert')->getUrl();
echo $file->effect('mirror')->getUrl();
```
We can apply more than one effect!

```php
echo $file->effect('flip')->effect('invert')->getUrl();
```

We can combine operations, not just effects.

Just chain methods and finish but calling "getUrl()".

```php
echo $file->resize(false, $height)->crop(100, 100)->effect('flip')->effect('invert')->getUrl();
```

`getUrl()` returns a string with the resulting URL.

However, it's optional – the object itself becomes a string when treated as such.

An example below will print an url too:

```php
echo $file->resize(false, $height)->crop(100, 100)->effect('flip')->effect('invert');
```

The way you provide operations matters.

We can see the same operations below, but result will be a little bit different because of order:

```php
echo $file->crop(100, 100)->resize(false, $height)->effect('flip')->effect('invert')->getUrl();
```

You can run any custom operations like this:

```php
echo $file->op('effect/flip');
echo $file->op('resize/400x400')->op('effect/flip');
```

You can call getUrl with postfix parameter. This is will add some readable postfix.

```php
echo $file->getUrl('image.jpg');
```

The result will be like this one:

    https://ucarecdn.com/85b5644f-e692-4855-9db0-8c5a83096e25/-/crop/970x500/center/he.jpg

[More information on file operations can be found here][2]

## Copy file

You can copy your file with all file operations like this:

```php
$new_file = $file->crop(200, 200)->effect('invert')->copy();
```

This will return a new Uploadcare\File object. This file will be cropped and
invert effect will be already applied to it.

You can also copy file like this:

```php
$new_file = $api->copyFile('https://ucarecdn.com/3ace4d6d-6ff8-4b2e-9c37-9d1cd0559527/-/resize/200x200/');
```

Sometimes storing the file in Uploadcare storage is not needed,
and we want to copy it directly to our custom S3 bucket. Here is how to
do it:
  1. Setup S3 storage from Dashboard -> Projet -> Custom Storage -> Connect S3 Bucket
     (as described here: https://uploadcare.com/documentation/storages/#setup)
  2. Run the following command:

```php
try {
  $file->copy("target_storage_name");
} catch (Exception $e) {
  echo $e->getMessage()."\n";
  echo nl2br($e->getTraceAsString())."\n";
}
```

## Uploading files
Let's have some fun with uploading files.

First of all, we can upload file from url. Just use construction below.

This will return Uploadcare\File instance.

```php
$file = $api->uploader->fromUrl('http://www.baysflowers.co.nz/Images/tangerine-delight.jpg');
$file->store();
```

By using default params of "fromUrl" method you tell Uploader to check file to be uploaded.

By default, Uploader will make 5 checks max with 1 second wait. You can change these params:

```php
$file = $api->uploader->fromUrl('http://www.baysflowers.co.nz/Images/tangerine-delight.jpg', true, $timeout, $max_attempts);
```

If file is not uploaded an Exception will be thrown.

You can just get token and check status manually later any time:

```php
$token = $api->uploader->fromUrl('http://www.baysflowers.co.nz/Images/tangerine-delight.jpg', false);
$data = $api->uploader->status($token);
if ($data->status == 'success') {
  $file_id = $data->file_id
  // do smth with a file
}
```

You can do any operations with this file now.

```php
echo $file->effect('flip')->getUrl();
```

You can upload file from path.

```php
$file = $api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
$file->store();
echo $file->effect('flip')->getUrl();
```

Or even just use a file pointer.

```php
$fp = fopen(dirname(__FILE__).'/test.jpg', 'r');
$file = $api->uploader->fromResource($fp);
$file->store();
echo $file->effect('flip')->getUrl();
```

The last thing you can do is upload a file just from it's contents. But you will have to provide mime-type.

```php
$content = "This is some text I want to upload";
$file = $api->uploader->fromContent($content, 'text/plain');
$file->store();
echo $file->getUrl();
```

If you want to delete file, just call delete() method on Uploadcare\File object.

```php
$file->delete();
```

## Custom User-Agent and CDN host

You can customize User-Agent reported during API requests (please do this if you're building a lib that is using uploadcare-php).
To do that pass a string with user agent name as third argument to Api constructor:

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY, "Awesome Lib/1.2.3");
```

You may also change default CDN host. You need to do this when you're using custom CNAME or you want to explicitly set your
[CDN provider](https://uploadcare.com/documentation/cdn/#alternative-domains).
To do that pass a string with domain name as fourth argument to Api constructor:

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY, null, "kx.ucarecdn.com");
```

## Tests

Inside "tests" directory you can find tests for PHP 5.3 and up.

These tests are based on PHPUnit, so you must have PHPUnit installed on your system to use them.

To execute the tests, run the following commands:

    phpunit

[1]: https://uploadcare.com/
[2]: https://uploadcare.com/documentation/cdn/
