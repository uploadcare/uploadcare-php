# Uploadcare PHP

This is a set of libraries to user with [Uploadcare][1].

## Install

Just clone source code anywhere you like inside your project:

    git clone git://github.com/uploadcare/uploadcare-php.git

If you like, define some constants with Public and Secret keys within your project:

    define('UC_PUBLIC_KEY', 'demopublickey');
    define('UC_SECRET_KEY', 'demoprivatekey');

If you are using PHP 5.3+ or 5.4+ it will be much better to use library with namespaces.
Just include one file to start using Uploadcare inside your PHP project and use namespace "\Uploadcare":

    require_once '../uploadcare/lib/5.3-5.4/Uploadcare.php';
    use \Uploadcare;

Now, we are ready. Create an object of Uploadcare\Api class:
    
    $api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

This is a main object your should work with. It has everything you need.

## Widgets and simple example

Let's start with widgets.

If you want to get Javascript's url for widget, just call:

    print $api-&gt;widget-&gt;getJavascriptUrl()

You can easily get all contents and &lt;script&gt; sections to include in your HTML:
    
    &lt;blockquote&gt;
    &lt;head&gt;
    &lt;?php print $api-&gt;widget-&gt;getInclude(); ?&gt;
    &lt;/head&gt;
    &lt;/blockquote&gt;

Or just this method to print:

    &lt;blockquote&gt;
    &lt;head&gt;
    &lt;?php $api-&gt;widget-&gt;printInclude(); ?&gt;
    &lt;/head&gt;
    &lt;/blockquote&gt;
    
Create some form to use with widget:

    &lt;blockquote&gt;
    &lt;form method="POST" action="upload.php"&gt;
      &lt;input type="hidden" role="uploadcare-uploader" name="qs-file" data-upload-url-base="" /&gt;
      &lt;input type="submit" value="Save!" /&gt;
     &lt;/form&gt;
     &lt;/blockquote&gt;
     
You will see an Uploadcare widget. After selecting file the "file_id" parameter will be set as value of hidden field.
 
The last thing left is to store file:
 
    $file_id = $_POST['qs-file'];
    $api = new Uploadcare\Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
    $file = $api-&gt;getFile($file_id);
    $file-&gt;store();
 
Now you have an Uploadcare\File object to work with. You can show an image like this:

    &lt;blockquote&gt;
    &lt;img src="&lt;?php echo $file-&gt;getUrl(); ?&gt;" /&gt;
    &lt;/blockquote&gt;

## API and requests

Ok, lets do some requests. This is request to index (http://api.uploadcare.com).

This will return an stdClass with information about urls you can request.

This is not really valuable data.

    $data = $api-&gt;request(API_TYPE_RAW);

Lets request account info.

This will return just some essential data inside stdClass such as: username, pub_key and email

    $account_data = $api-&gt;request(API_TYPE_ACCOUNT);

Now lets get file list.

This request will return stdClass with all files uploaded and some information about files.

Each files has:

* size
* upload_date
* last_keep_claim
* on_s3
* made_public
* url
* is_image
* file_id
* original_filename
* removed
* mime_type
* original_file_url

    $files_raw = $api-&gt;request(API_TYPE_FILES);

Previous request is just some raw request and it will return raw data from json.

There's a better way to handle all the files by using method below.

It will return an array of \Uploadcare\File objects to work with.

This objects don't provide all the data like in previous request, but provides ways to display the file 
and to use methods such as resize, crop, etc 

    $files = $api-&gt;getFileList();

If you have a file_id (for example, it's saved in your database) you can create object for file easily.

Just use request below:

    $file_id = '5255b9dd-f790-425e-9fa9-8b49d4e64643';
    $file = $api-&gt;getFile($file_id);

## File operations

Using object of \Uploadcare\File class we can get url for the file

    echo $file-&gt;getUrl();

Now let's do some crop.

    $width = 400;
    $height = 400;
    $is_center = true;
    $fill_color = 'ff0000';
    echo $file-&gt;crop($width, $height, $is_center, $fill_color)-&gt;getUrl();

And here's some resize with width and height

    echo $file-&gt;resize($width, $height)-&gt;getUrl();

Width only

    echo $file-&gt;resize($width)-&gt;getUrl();

Height only

    echo $file-&gt;resize(false, $height)-&gt;getUrl();

We can also use scale crop
    
    echo $file-&gt;scaleCrop($width, $height, $is_center)-&gt;getUrl();

And we can apply some effects.

    echo $file-&gt;applyFlip()-&gt;getUrl();
    echo $file-&gt;applyGrayscale()-&gt;getUrl();
    echo $file-&gt;applyInvert()-&gt;getUrl();
    echo $file-&gt;applyMirror()-&gt;getUrl();

We can apply more than one effect!

    echo $file-&gt;applyFlip()-&gt;applyInvert()-&gt;getUrl();

We can combine operations, not just effects.

Just chain methods and finish but calling "getUrl()".

    echo $file-&gt;resize(false, $height)-&gt;crop(100, 100)-&gt;applyFlip()-&gt;applyInvert()-&gt;getUrl();

The way you provide operations matters.

We can see the same operations below, but result will be a little bit different because of order:

    echo $file-&gt;crop(100, 100)-&gt;resize(false, $height)-&gt;applyFlip()-&gt;applyInvert()-&gt;getUrl();

## Uploading files
Let's have some fun with uploading files.

First of all, we can upload file from url. Just use construction below.

This will return Uploadcare\File instance.

    $file = $api-&gt;uploader-&gt;fromUrl('http://www.baysflowers.co.nz/Images/tangerine-delight.jpg');
    $file-&gt;store();

You can do any operations with this file now.
    
    echo $file-&gt;applyFlip()-&gt;getUrl();

You can upload file from path.

    $file = $api-&gt;uploader-&gt;fromPath(dirname(__FILE__).'/test.jpg');
    $file-&gt;store();
    echo $file-&gt;applyFlip()-&gt;getUrl();

Or even just use a file pointer.

    $fp = fopen(dirname(__FILE__).'/test.jpg', 'r');
    $file = $api-&gt;uploader-&gt;fromResource($fp);
    $file-&gt;store();
    echo $file-&gt;applyFlip()-&gt;getUrl();

The last thing you can do is upload a file just from it's contents. But you will have to provide mime-type.

    $content = "This is some text I want to upload";
    $file = $api-&gt;uploader-&gt;fromContent($content, 'text/plain');
    $file-&gt;store();
    echo $file-&gt;getUrl();

If you want to delete file, just call delete() method on Uploadcare\File object.
    
    $file-&gt;delete();

[1]: https://uploadcare.com/