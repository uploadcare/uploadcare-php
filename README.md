# Uploadcare PHP

Uploadcare PHP integration handles uploads by wrapping Upload and REST APIs.

[![Build Status][travis-img]][travis] [![Uploadcare stack on StackShare][stack-img]][stack]  

[travis-img]: https://api.travis-ci.org/uploadcare/uploadcare-php.svg?branch=master
[travis]: https://travis-ci.org/uploadcare/uploadcare-php
[stack-img]: http://img.shields.io/badge/tech-stack-0690fa.svg?style=flat
[stack]: https://stackshare.io/uploadcare/stacks/

* [Requirements](#requirements)
* [Install](#install)
* [Usage](#usage)
  * [Uploadcare Widget and simple example](#uploadcare-widget-and-simple-example)
  * [API and requests](#api-and-requests)
  * [Groups](#groups)
  * [File operations](#file-operations)
  * [Copy file](#copy-file)
  * [Uploading files](#uploading-files)
  * [Custom User-Agent and CDN host](#custom-user-agent-and-cdn-host)
  * [Signed uploads](#signed-uploads)
  * [Tests](#tests)
* [Useful links](#useful-links)

## Requirements

- `php5.3+`
- `php-curl`
- `php-json`

## Install

Prior to installing `uploadcare-php` check if you're using the [Composer](getcomposer.org) dependency manager for PHP. If not, we well recommend you considering it.

**Step 1** — update your `composer.json` with,

```js
"require": {
    "uploadcare/uploadcare-php": ">=v1.1.0,<2.0"
}
```

**Step 2** — run [Composer](https://getcomposer.org),

```bash
php composer.phar update
```

**Step 3** — define your Uploadcare public and secret API [keys](https://uploadcare.com/documentation/keys/) as global constants (that's optional yet pretty useful),

```php
define('UC_PUBLIC_KEY', 'demopublickey');
define('UC_SECRET_KEY', 'demosecretkey');
```

**Step 4** — include the following file and use `\Uploadcare` namespace,

```php
require_once 'vendor/autoload.php';
use \Uploadcare;
```

**Step 5** — create an object of the `Uploadcare\Api` class,

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
```

That's it, all further operations are performed using the object.

## Usage
### Uploadcare Widget and simple example

Let's start with adding [Uploadcare Widget](https://uploadcare.com/documentation/widget/).

Widget JavaScript URL can be obtained by calling this:

```php
print $api->widget->getScriptSrc()
```

You can get all contents and &lt;script&gt; sections to include in your HTML,

```html
<head>
    <?php print $api->widget->getScriptTag(); ?>
</head>
```

Now, let's make a from to use with the widget,

```html
<form method="POST" action="upload.php">
    <?php echo $api->widget->getInputTag('qs-file'); ?>
    <input type="submit" value="Save!" />
</form>
```

Once finished, you'll see the widget. Use it to pick any file. Upon selecting, the "file_id" parameter will be set to a hidden field value.

You might then want to `store()` a file. Keep in mind that we remove files that weren't stored within 24 hours.

```php
$file_id = $_POST['qs-file'];
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
$file = $api->getFile($file_id);
$file->store();
``````

Now, you got an `Uploadcare\File` object to work with. For instance, this is how you show an image,

```php
<img src="<?php echo $file->getUrl(); ?>" />
```

That can be put even simpler,

```php
<img src="<?php echo $file; ?>" />
```

Or you might want to call the `getImgTag` method. This returns a `<img>` tag:

```php
echo $file->getImgTag('image.jpg', array('alt' => 'Image'));
```

### API and requests

You can do any simple request if you like by calling:

`Uploadcare\Api` allows you to make simple requests to Uploadcare endpoints. Using requests might be the case when you'd like no UI to be involved with file routines.

```php
$api->request($method, $path, $data = array(), $headers = array());
```

Keep in mind that each API endpoint has its own allowed methods. If you're using a method that's not allowed with an endpoint, exceptions will be thrown.

Here's a request to the API index, https://api.uploadcare.com. It will return `stdClass` holding info about URLs you can request,

```php
$data = $api->request('GET', '/');
```

Now, let's request account info. This will return some essential
data within `stdClass`, such as `username`, `public_key`, and `email`,

```php
$account_data = $api->request('GET', '/account/');
```


Now lets get a list of files. Let's form a request that will return
`stdClass` holding all the uploaded files and respective metadata,

```php
$files_raw = $api->request('GET', '/files/');
```

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

The request we just made was a raw kind returning raw data. There's a better way to handle files, a method that returns an array of `\Uploadcare\File` to work with. `\File` objects provide better ways of displaying images and applying methods like resize, crop, etc.

```php
$files = $api->getFileList();
// $files is Iterator instance, so you don't have to use pagination explicitly:
foreach ($files as $file) {
    // do something with file
    echo $file->getUrl();
}
```

If you have file UUID or CDN URL (you might be storing those in your database),
file object can be created in no time,

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

Accessing the `data` parameter will fire a GET request to get all
that data at once. The returned array is cached in case you'd like to
access the `data` parameter again.

### Groups

Files can be joined into groups. Group is a way to store many files under a single UUID.

Here's how you get a list of existing groups,

```php
$from = '2017-10-10';
$api->getGroupList($from);
```

`$from` parameter was not required and is usually used to filter returned lists.

Upon request completion, you'll get an array of `Group` objects. Accessing a single group is done like this,

```php
$group_id = 'badfc9f7-f88f-4921-9cc0-22e2c08aa2da~12';
$group = $api->getGroup($group_id);
```

Retrieving files from the group,

```php
$files = $group->getFiles();
```

Storing a group,

```php
$group->store();
```

### File operations

As you already know from the previous sections,
file URL can be obtained by using the `\Uploadcare\File` class.

```php
echo $file->getUrl();
```

If your file is an image and you'd like to do some cropping,
here's the example,

```php
$width = 400;
$height = 400;
$is_center = true;
$fill_color = 'ff0000';
echo $file->crop($width, $height, $is_center, $fill_color)->getUrl();
```

This one is for image resize with two dimensions,

```php
echo $file->resize($width, $height)->getUrl();
```

Width-only image resize, aspect ratio is preserved,

```php
echo $file->resize($width)->getUrl();
```

Height-only image resize,

```php
echo $file->resize(false, $height)->getUrl();
```

Scale crop,

```php
echo $file->scaleCrop($width, $height, $is_center)->getUrl();
```

Applying effects to an image,

```php
echo $file->effect('flip')->getUrl();
echo $file->effect('grayscale')->getUrl();
echo $file->effect('invert')->getUrl();
echo $file->effect('mirror')->getUrl();
```

Applying multiple effects at once,

```php
echo $file->effect('flip')->effect('invert')->getUrl();
```

Actually, not only image effects but operations too can be combined and even mixed. That's done through chaining methods and calling `getUrl()` in the end.

```php
echo $file->resize(false, $height)->crop(100, 100)->effect('flip')->effect('invert')->getUrl();
```

Even though `getUrl()` returns a string with a resulting URL, it is optional in the example above. Thing is, a file object itself becomes a string when used like this.

So, this example will print an URL too,

```php
echo $file->resize(false, $height)->crop(100, 100)->effect('flip')->effect('invert');
```

Keep in mind that the order of operations is important. Depending on how you chain operations, results will vary,

```php
echo $file->crop(100, 100)->resize(false, $height)->effect('flip')->effect('invert')->getUrl();
```

Here's a way to run custom operations,

```php
echo $file->op('effect/flip');
echo $file->op('resize/400x400')->op('effect/flip');
```

You can call `getUrl()` with a custom postfix parameter. This is intended to
add a readable postfix,

```php
echo $file->getUrl('image.jpg');
```

Here's how the URL might look like with the above-mentioned postfix,

```
https://ucarecdn.com/85b5644f-e692-4855-9db0-8c5a83096e25/-/crop/970x500/center/image.jpg
```

More info about file operations can be found in our [docs](https://uploadcare.com/documentation/cdn/).

### Copying files

Copying might be used to optimize your file infrastructure. For instance, we can copy an image with all the applied operations,

```php
$new_file = $file->crop(200, 200)->effect('invert')->copy();
```

This returns a new `Uploadcare\File object` with recently assigned UUID. Hence, all image effects will become permanent after copying.

Here's another way to copy a file,

```php
$new_file = $api->createLocalCopy('https://ucarecdn.com/3ace4d6d-6ff8-4b2e-9c37-9d1cd0559527/-/resize/200x200/');
```

You might not want to use Uploadcare storage for your files. Instead, you'd like them to go directly to your custom S3 bucket. It's fine with us and here's how to setup this behavior:

* [Setup S3 storage](https://uploadcare.com/documentation/storages/#setup)
  from "Dashboard -> Projeсt -> Custom Storage -> Connect S3 Bucket".
* Run the following command,

```php
try {
    $file->createRemoteCopy("target_storage_name");
} catch (Exception $e) {
    echo $e->getMessage()."\n";
    echo nl2br($e->getTraceAsString())."\n";
}
```

### Uploading files

This section describes multiple ways of uploading files to Uploadcare.

First of, files can be uploaded **from URL**. The following returns an instance of `Uploadcare\File`,

```php
$file = $api->uploader->fromUrl('http://www.baysflowers.co.nz/Images/tangerine-delight.jpg');
$file->store();
```

Using `fromUrl()` with default params tells Uploadcare to check file availability prior to upload. By default, there will be 5 check attempts
with 1-second timeouts in between. These can be changed:

```php
$file = $api->uploader->fromUrl(
    'http://www.baysflowers.co.nz/Images/tangerine-delight.jpg',
    array(
        'filename' => 'image.jpeg,
        'check_status' => true,
        'timeout' => $timeout,
        'max_attempts' => $max_attempts
    )
);
```

In case `fromUrl()` file uploading attempts failed, an exception is thrown. Later on, the operation status can be re-checked by utilizing a token,

```php
$token = $api->uploader->fromUrl(
    'http://www.baysflowers.co.nz/Images/tangerine-delight.jpg',
    array('check_status' => false)
);
$data = $api->uploader->status($token);
if ($data->status == 'success') {
    $file_id = $data->file_id
    // do smth with a file
}
```
Once a file is uploaded, you can do any operations with this file,

```php
echo $file->effect('flip')->getUrl();
```

Another way of uploading files is **from a path**,

```php
$file = $api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
$file->store();
echo $file->effect('flip')->getUrl();
```

This will also do when using file pointers,

```php
$fp = fopen(dirname(__FILE__).'/test.jpg', 'r');
$file = $api->uploader->fromResource($fp);
$file->store();
echo $file->effect('flip')->getUrl();
```

There's also an option of uploading a file **from its contents**. This will require you to provide MIME-type,

```php
$content = "This is some text I want to upload";
$file = $api->uploader->fromContent($content, 'text/plain');
$file->store();
echo $file->getUrl();
```

### Deleting files

Files are deleted by using the `delete()` method on `Uploadcare\File`
objects,

```php
$file->delete();
```

### Custom User-Agent and CDN host

You can customize User-Agent reported during API request. Please do this if you're building a lib that uses uploadcare-php. To do so, pass a string holding User-Agent name into the API constructor as the third argument,

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY, "Awesome Lib/1.2.3");
```

The better way to report User-Agent HTTP header is to set a framework name (ex: Wordpress, Laravel) and an extension name (ex: PHPUploadcare-Wordpress) with versions, if exists:

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
$api->setFramework('Wordpress', '4.9.5');
$api->setExtension('PHPUploadcare-Wordpress', '2.6.0');
```

You can also change the default CDN host. That's needed when you're using custom CNAME, or you are willing to explicitly set your [CDN provider](https://uploadcare.com/documentation/cdn/#alternative-domains). That's done through passing a domain name string into the API constructor as the fourth argument,

```php
$api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY, null, "cdn.example.com");
```

### Signed uploads

Signed Uploads let you control who and when can upload files to a specified Uploadcare project.

Just pass expire lifetime in seconds:

```php
$api = new Uploadcare\Api(
    UC_PUBLIC_KEY,
    UC_SECRET_KEY,
    null,
    "cdn.example.com",
    null,
    null,
    30 * 60 // 30 minutes expire time
);
```

See more about it in [documentation](https://uploadcare.com/docs/api_reference/upload/signed_uploads/)

### Tests

PHP 5.3+ tests can be found in the "tests" directory. The tests are based on PHPUnit, so you must have it installed on your system to use those.

Tests are executed using the `phpunit` command.

## Useful links

[Uploadcare documentation](https://uploadcare.com/docs/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[Upload API reference](https://uploadcare.com/api-refs/upload-api/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[REST API reference](https://uploadcare.com/api-refs/rest-api/?utm_source=github&utm_medium=referral&utm_campaign=uploadcare-php)  
[Changelog](https://github.com/uploadcare/uploadcare-php/blob/master/CHANGELOG.md)  
[Contributing guide](https://github.com/uploadcare/.github/blob/master/CONTRIBUTING.md)  
[Security policy](https://github.com/uploadcare/uploadcare-php/security/policy)  
[Support](https://github.com/uploadcare/.github/blob/master/SUPPORT.md)  
