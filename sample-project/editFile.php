<?php
  require_once 'config.php';
  require_once '../vendor/autoload.php';
  use Uploadcare\Api;
  
  $fileId = null;
  $fileIdParam = "fileId";
  
  $action = null;
  $actionParam = "action";

  $store = null;
  $storeParam = "store";
  
  $targetStorage = null;
  $targetStorageParam = "targetStorage";
  
  $pattern = null;
  $patternParam = "pattern";
  
  $makePublic = null;
  $makePublicParam = "makePublic";

  
  if(array_key_exists($fileIdParam, $_GET)) {
    $fileId = $_GET[$fileIdParam];
  }
  
  if(array_key_exists($actionParam, $_POST)) {
    $action = $_POST[$actionParam];
  }
  
  if(array_key_exists($storeParam, $_POST)) {
    $store = $_POST[$storeParam];
  }
  
  if(array_key_exists($targetStorageParam, $_POST)) {
    $targetStorage = $_POST[$targetStorageParam];
  }
  
  if(array_key_exists($patternParam, $_POST)) {
    $pattern = $_POST[$patternParam];
  }
  
  if(array_key_exists($makePublicParam, $_POST)) {
    $makePublic = $_POST[$makePublicParam];
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
    $actionResult = null;
    $actionError = null;
    $state = $data["datetime_removed"] ?
                '<span class="label label-danger">Deleted</span>' :
                  ($data["datetime_stored"] ?
                    '<span class="label label-success">Stored</span>' :
                    '<span class="label label-primary">Uploaded</span>');
    
    try {
      switch($action) {
        case 'localCopy':
          $isStore = $store ? true : false;
          $res = $api->createLocalCopy($uuid, $isStore);
          $actionResult = "File successfully copied inside Uploadcare storage with id: " . $res->getUuid();
        break;
        case 'remoteCopy':
          $isMakePublic = $makePublic ? true : false;
          $res = $api->createRemoteCopy($uuid, $targetStorage, $isMakePublic, $pattern);
          $actionResult = "File successfully copied into external storage and available at: " . $res;
        break;
        case 'delete':
          $res = $file->delete();
          $actionResult = "File successfully deleted";
          $state = '<span class="label label-danger">Deleted</span>';
        break;
      }
    } catch (Exception $ex) {
      $actionError = $ex->getMessage();
    }
    
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
        <a href="fileList.php">Return to file list</a>
      </div>
<?php if(!!$actionResult) { ?>
        <p>&nbsp;</p>
        <div class="col-md-12">
          <div class="alert alert-success" role="alert">
            <?php echo($actionResult); ?>
          </div>
        </div>
<?php } ?>
<?php if(!!$actionError) { ?>
        <p>&nbsp;</p>
        <div class="col-md-12">
          <div class="alert alert-danger" role="alert">
            <?php echo($actionError); ?>
          </div>
        </div>
<?php } ?>
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
              <th>File name:</th>
              <td>
                <?php
                  echo($fileName);
                ?>
            </td>
            </tr>
            <tr>
              <th>File Id:</th>
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
            <tr>
              <th>State:</th>
              <td>
                <?php
                  echo($state);
                ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">Copy file within Uploadcare storage</div>
          <div class="panel-body">
            <form action="" method="POST">
              <div class="form-group">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="store"> Store file
                  </label>
                </div>
              </div>
              <input type="hidden" name="action" value="localCopy">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Copy within Uploadcare storage</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">Copy file to external storage</div>
          <div class="panel-body">
            <form action="" method="POST">
              <div class="form-group">
                <label for="target">Target Storage</label>
                <input type="text" class="form-control" name="targetStorage" placeholder="Storage name">
              </div>
              <div class="form-group">
                <label for="pattern">Destination file name pattern</label>
                <input type="text" class="form-control" name="pattern" placeholder="Filename pattern" value="${auto_filename}">
                You can use following pattern variables:
                <ul>
                  <li>${default} = ${uuid}/${auto_filename}</li>
                  <li>${auto_filename} = ${filename} ${effects} ${ext}</li>
                  <li>${effects} = CDN operations put into a URL</li>
                  <li>${filename} = original filename, no extension</li>
                  <li>${uuid} = file UUID</li>
                  <li>${ext} = file extension, leading dot, e.g. .jpg</li>
                </ul>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="makePublic"> Make public
                  </label>
                </div>
              </div>
              <input type="hidden" name="action" value="remoteCopy">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Copy file to external storage</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">Delete file</div>
          <div class="panel-body">
            <form action="" method="POST">
              <input type="hidden" name="action" value="delete">
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
