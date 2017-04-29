<?php
require_once 'config.php';
require_once '../vendor/autoload.php';
use Uploadcare\Api;
  
  $fileId = null;
  $fileIdParam = "fileId";
  
  $opType = null;
  $opTypeParam = "type";
  
  $targetStorage = null;
  $targetStorageParam = "target";
  
  $storeSourceFile = false;
  $storeSourceFileParam = "storeSourceFile";
  
  $api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

  if(array_key_exists($fileIdParam, $_POST)) {
    $fileId = $_POST[$fileIdParam];
  }
  
  if(array_key_exists($opTypeParam, $_POST)) {
    $opType = $_POST[$opTypeParam];
  }
  
  if(array_key_exists($targetStorageParam, $_POST)) {
    $targetStorage = $_POST[$targetStorageParam];
  }
  
  if(array_key_exists($storeSourceFileParam, $_POST)) {
    $storeSourceFile = !!$_POST[$storeSourceFileParam];
  }
  
  $file = $api->getFile($fileId);
  $errors = array();
  
  $newFile = null;
  $newUUID = null;
  
  switch ($opType) {
    case 'copy':
      $newFile = $api->createLocalCopy($fileId, $storeSourceFile);
      if(!$newFile) {
        array_push($errors, "Error on operation");
      } else {
        $newUUID = $newFile->getUuid();
      }
      break;
    
    case 'delete':
      # code...
      break;
      
    default:
      array_push($errors, "Invalid operation type");
      break;
  }
?>

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
        <center><h2>Results</h2></center>
        <br/>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
<?php
  if(count($errors)) {
?>
        <div class="alert alert-danger" role="alert">
          <ul>
            <?php
              foreach ($errors as &$err) {
                echo("<li>${err}</li>");
              }
            ?>
          </ul>
        </div>
<?php 
  } else {
    
    switch($opType) {
      case 'copy':
        echo("<b>File copied successfully </b><a href=\"editFile.php?fileId=${newUUID}\">Edit</a>");
        break;
      case 'delete':
        
        break;
    }
?>
<?php 
  }
?>        
      </div>
    </div>
  </div>
</body>
</html>