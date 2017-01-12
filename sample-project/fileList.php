<?php
require_once 'config.php';
require_once '../vendor/autoload.php';
use Uploadcare\Api;
  
  $pageLimit = 10;
  $from = null;
  $to = null;

  $api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

  $fromParam = "from";
  $toParam = "to";
  if(array_key_exists($fromParam, $_GET)) {
    $from = $_GET[$fromParam];
  }
  if(array_key_exists($toParam, $_GET)) {
    $to = $_GET[$toParam];
  }
  
  $files = $api->getFileList(array(
    'limit' => $pageLimit,
    'from' => $from,
    'to' => $to
  ));

?>
<!DOCTYPE html>
<html>
<head>
<meta encoding='utf-8'>
<title>Uploadcare file list</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h3>File list</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
   
        <table class="table table-striped">
          <thead>
            <tr>
              <th>GUID</th>
              <th>File name</th>
              <th>Is image</th>
              <th>Size</th>
            </tr>
          </thead>
          <tboby>
          <?php 
            foreach ($files as $key => $value) {
              $uuid = $value->getUuid();
              $data = $value->__get('data');
              $fileName = $data["original_filename"];              
              $isImage = $data["is_image"] == 1 ? 'true' : 'false';
              $size = $data["size"];

              echo (<<<EOT
              <tr>
              <td>${uuid}</td>
              <td>${fileName}</td>
              <td>${isImage}</td>
              <td>${size}</td>
              <tr>
EOT
);
            }
          ?>
          </tboby>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="...">
          <ul class="pager">
            <li class="previous"><a href="
            <?php echo("?".http_build_query($files->getPrevPageParams())) ?>">Previous</a></li>
            <li class="next"><a href="<?php echo("?".http_build_query($files->getNextPageParams())) ?>">Next</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
</body>
</html>