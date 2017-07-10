<?php
require_once 'config.php';
require_once '../vendor/autoload.php';
use Uploadcare\Api;
  
  $pageLimit = 10;
  $from = null;
  $to = null;
  $reversed = false;
  $removed = false;

  $api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

  $fromParam = "from";
  $toParam = "to";
  $reversedParam = "reversed";
  $limitParam = "limit";
  $removedParam = "removed";
  
  $offset = 0;
  $reversed = false;
  if(array_key_exists($fromParam, $_GET)) {
    $from = $_GET[$fromParam];
  }
  if(array_key_exists($limitParam, $_GET)) {
    $pageLimit = $_GET[$limitParam];
  }
  if(array_key_exists($toParam, $_GET)) {
    $to = $_GET[$toParam];
  }
  if(array_key_exists($removedParam, $_GET)) {
    $removed = $_GET[$removedParam];
  }
  if(array_key_exists($reversedParam, $_GET)) {
    $reversed = $_GET[$reversedParam] == "1" ? true : false;
  }
  
  $files = $api->getFileList(array(
    'limit' => $pageLimit,
    'from' => $from,
    'to' => $to,
    'removed' => !!$removed,
    'offset' => 0,
    'reversed' => $reversed
  ));

  $cnt = $files->count();

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
              <th>Image</th>
              <th>Size</th>
            </tr>
          </thead>
          <tboby>
          <?php 
            foreach ($files as $key => $value) {
              $uuid = $value->getUuid();
              $data = $value->__get('data');
              $fileName = $data["original_filename"];
              $imageUrl = $value->preview(50, 50)->getUrl();
              $size = $data["size"];

              echo (<<<EOT
              <tr>
              <td>${uuid}</td>
              <td>${fileName}</td>
              <td><img src="${imageUrl}"/></td>
              <td>${size}</td>
              <tr>
EOT
);
            }
            $prevPageQuery = $files->getPrevPageQuery();
            $nextPageQuery = $files->getNextPageQuery();
          ?>
          </tboby>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="...">
          <ul class="pager">
          <?php if($prevPageQuery) { ?>
            <li class="previous"><a href="
              <?php echo("?".$prevPageQuery) ?>
            ">Previous</a></li>
          <?php } ?>
          <?php if($nextPageQuery) { ?>
            <li class="next"><a href="
              <?php echo("?".$nextPageQuery) ?>
            ">Next</a></li>
          <?php } ?>
          </ul>
        </nav>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
</body>
</html>
