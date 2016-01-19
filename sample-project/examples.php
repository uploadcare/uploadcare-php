<?php
/**
 * Examples
 */
// This is just some config with public and secret keys for UC.
require_once 'config.php';
// requesting autoloader that got uploadcare in there
require_once 'vendor/autoload.php';
// using api
use Uploadcare\Api;

// create object instance for Api.
$api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

/**
 * Let's start with widgets.
 * You can get widget url by using this:
* */
print $api->widget->getScriptSrc()."\n";

/**
 * You can just use method below to get all the code to insert widget
 */
print $api->widget->getScriptTag()."\n";

/**
 * Ok, lets do some requests. This is request to index (http://api.uploadcare.com).
 * This will return an stdClass with information about urls you can request.
 */
$data = $api->request('GET', '/');

/**
 * Ok, now lets get file list.
 * This request will return stdClass with all files uploaded and some information about files.
 * Each files has:
 *  - size
 *  - upload_date
 *  - last_keep_claim
 *  - on_s3
 *  - made_public
 *  - url
 *  - is_image
 *  - uuid
 *  - original_filename
 *  - removed
 *  - mime_type
 *  - original_file_url
 *
*/
$files_raw = $api->request('GET', '/files/');

/**
 *  Previous request is just some raw request and it will return raw data from json.
 *  There's a better way to handle all the files by using method below.
 *  It will return an array of \Uploadcare\File objects to work with.
 *
 *  This objects don't provide all the data like in previous request, but provides ways to display the file
 *  and to use methods such as resize, crop, etc
*/
$files = $api->getFileList();

echo "We have ".count($files)." file(s)\n";

/**
 * Listing all file ids
 */

/** @var Uploadcare\File $file */
foreach ($files as $file)
{
  echo $file->getUuid()."\n";
}

/**
 * Listing only first 4 file ids (if you want to use pagination)
 */
$page = 1;
$per_page = 4;

for ($i = ($page - 1) * $per_page; $i < min(count($files), $page * $per_page); $i++)
{
  echo $file->getUuid()."\n";
}

/**
 * If you have a file_id (for example, it's saved in your database) you can create object for file easily.
 * Just user request below
*/
$file_id = '5255b9dd-f790-425e-9fa9-8b49d4e64643';
$file = $api->getFile($file_id);

/**
 * Ok, using object of \Uploadcare\File class we can get url for the file
*/
echo $file->getUrl()."\n";

/**
 * Or even get an image tag
 */
echo $file->getImgTag('image.jpg', array('alt' => 'Somealt'))."\n";

/**
 * Now let's do some crop.
 */
$width = 400;
$height = 400;
$is_center = true;
$fill_color = 'ff0000';
echo $file->crop($width, $height, $is_center, $fill_color)->getUrl()."\n";

/**
 * And here's some resize with width and height
 * */
echo $file->resize($width, $height)->getUrl()."\n";

/**
 * Width only
 */
echo $file->resize($width)->getUrl()."\n";

/**
 * Height only
 */
echo $file->resize(false, $height)->getUrl()."\n";

/**
 * We can also use scale crop
 */
echo $file->scaleCrop($width, $height, $is_center)->getUrl()."\n";

/**
 * And we can apply some effects.
 */
echo $file->effect('flip')->getUrl()."\n";
echo $file->effect('grayscale')->getUrl()."\n";
echo $file->effect('invert')->getUrl()."\n";
echo $file->effect('mirror')->getUrl()."\n";

/**
 * We can apply more that one effect!
 * */
echo  $file->effect('flip')->effect('invert')->getUrl()."\n";

/**
 * We can combine operations, not just effects.
 *
 * Just chain methods and finish but calling "getUrl()".
 *
 * */
echo $file->resize(false, $height)->crop(100, 100)->effect('flip')->effect('invert')->getUrl()."\n";

/**
 * The way you provide operations matters.
 * We can see the same operations below, but result will be a little bit different.
 */
echo $file->crop(100, 100)->resize(false, $height)->effect('flip')->effect('invert')->getUrl()."\n";

/**
 * You can run any custom operations like this:
 */
echo $file->op('effect/flip')."\n";
echo $file->op('resize/400x400')->op('effect/flip')."\n";

/**
 * You can call getUrl with postfix parameter. This is will add some readable postfix.
 */
echo $file->getUrl('image.jpg')."\n";

/**
 * You can find more about operations here:
 * https://uploadcare.com/documentation/reference/basic/cdn.html
 */

/**
 * Ok, it's everything with operations.
 * Let's have some fun with uploading files.
 * First of all, we can upload file from url. Just use construction below.
 * This will return File instance.
 */
$file = $api->uploader->fromUrl('https://www.baysflowers.co.nz/wp-content/uploads/2015/06/IMG_9886_2.jpg');

/**
 * File must be uploaded, but it's not stored yet.
 * Let's store it.
 * We user true flag to be sure that file is uploaded.
**/
try {
  $file->store(true);
} catch (Exception $e) {
  echo $e->getMessage()."\n";
  echo nl2br($e->getTraceAsString())."\n";
}

/**
 * Sometimes storing the file in Uploadcare storage is not needed,
 * and we want to copy it directly to our custom S3 bucket. Here is how to
 * do it:
 *   1. Setup S3 storage from Dashboard -> Projet -> Custom Storage -> Connect S3 Bucket
 *      (as described here: https://uploadcare.com/documentation/storages/#setup)
 *   2. Run the following command:
 **/
try {
  $file->copy("target_storage_name");
} catch (Exception $e) {
  echo $e->getMessage()."\n";
  echo nl2br($e->getTraceAsString())."\n";
}

/**
 * We can do any operations with this file now.
 **/
echo $file->effect('flip')->getUrl()."\n";

/**
 * We can upload file from path
 * */
$file = $api->uploader->fromPath(dirname(__FILE__).'/test.jpg');
$file->store();
echo $file->effect('flip')->getUrl()."\n";

/**
 * Or even just use a file pointer.
 **/
$fp = fopen(dirname(__FILE__).'/test.jpg', 'r');
$file = $api->uploader->fromResource($fp);
$file->store();
echo $file->effect('flip')->getUrl()."\n";

/**
 * The last thing you can do is upload a file just from it's contents. But you will have to provide
 * mime-type.
 */
$content = "This is some text I want to upload";
$file = $api->uploader->fromContent($content, 'text/plain');
$file->store();
echo $file->getUrl()."\n";

/**
 * Lets delete the last file.
 */
$file->delete();
