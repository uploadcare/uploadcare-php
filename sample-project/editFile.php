<?php
  require_once 'config.php';
  require_once '../vendor/autoload.php';
  use Uploadcare\Api;
  
  $fileId = null;
  $fileIdParam = "fileId";
  
  if(array_key_exists($fileIdParam, $_GET)) {
    $fileId = $_GET[$fileIdParam];
  }
  
  $api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
  
  if($fileId) {
    $file = $api->getFile($fileId);
    $imgUrl = $file->preview(400, 400)->getUrl();
    $uuid = $file->getUuid();
    $data = $file->__get('data');
    $fileName = $data["original_filename"];
    $imgInfo = $data["image_info"];
    $height = $imgInfo->height;
    $width = $imgInfo->width; 
    $format = $imgInfo->format;
    $size = $data["size"];
  }

?>
<!DOCTYPE html>
<html>
<head>
<meta encoding='utf-8'>
<title>Uploadcare edit file</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <center><h2>Edit file</h2></center>
        <br/>
      </div>
    </div>
    <div class="row">
<?php 
  if(!!$uuid) {
?>
      <div class="col-md-6">
      <center>
        <?php
          echo(<<<EOT
          <img src="${imgUrl}" alt="">
EOT
);
        ?>
        <p>Image resized to fit 400x400</p>
        </center>
      </div>
      
      <div class="col-md-6">
        <table class="table table-striped">
          <tbody>
            <tr>
              <th>Image file name:</th>
              <td>
                <?php
                  echo($fileName);
                ?>
            </td>
            </tr>
            <tr>
              <th>Image Id:</th>
              <td>
                <?php
                  echo($uuid);
                ?>
            </td>
            </tr>
            <tr>
              <th>Original size:</th>
              <td>
                <?php
                  echo("${width}x${height}, ${size}b");
                ?>
              </td>
            </tr>
            <tr>
              <th>Format:</th>
              <td>
                <?php
                  echo($format);
                ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-heading">Copy resized image within current storage</div>
          <div class="panel-body">
            <form action="processFile.php" method="POST">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="storeSourceFile"> Store source file
                </label>
              </div>
              <input type="hidden" name="fileId" value="<?php echo("${imgUrl}") ?>">
              <input type="hidden" name="type" value="copy">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Copy</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-heading">Copy resized image to enother storage</div>
          <div class="panel-body">
            <form action="processFile.php" method="POST">
              <div class="form-group">
                <label for="target">Target Storage</label>
                <input type="text" class="form-control" name="target" placeholder="Storage name">
              </div>
              <input type="hidden" name="fileId" value="<?php echo("${uuid}") ?>">
              <input type="hidden" name="type" value="copy">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Copy</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-heading">Delete image</div>
          <div class="panel-body">
            <form action="processFile.php" method="POST">
              <input type="hidden" name="fileId" value="<?php echo("${uuid}") ?>">
              <input type="hidden" name="type" value="delete">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Delete</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
<?php
  }
?>
  </div>
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
</body>
</html>
