<?php
require_once 'config.php';
require_once '../vendor/autoload.php';
use Uploadcare\Api;
  
  $pageLimit = 5;
  $from = null;
  $to = null;
  $reversed = false;

  $api = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);

  $fromParam = "from";
  $toParam = "to";
  $reversedParam = "reversed";
  $offset = 0;
  $reversed = false;
  if(array_key_exists($fromParam, $_GET)) {
    $from = $_GET[$fromParam];
  }
  if(array_key_exists($toParam, $_GET)) {
    $to = $_GET[$toParam];
  }

  if(array_key_exists($reversedParam, $_GET)) {
    $reversed = $_GET[$reversedParam] == "1" ? true : false;
  }
  
  $groups = $api->getGroupList(array(
    'limit' => $pageLimit,
    'from' => $from,
    'to' => $to,
    'removed' => false,
    'offset' => 0,
    'reversed' => $reversed
  ));
  $cnt = $groups->count();
  

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
        <h3>Group list</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
   
        <table class="table table-striped">
          <thead>
            <tr>
              <th>GUID</th>
              <th>Files Count</th>
            </tr>
          </thead>
          <tboby>
          <?php 
          foreach ($groups as $key => $value) {
              $uuid = $value->getGroupId();
              $filesCnt = $value->getFilesQty();

              echo (<<<EOT
              <tr>
              <td>${uuid}</td>
              <td>${filesCnt}</td>
              <tr>
EOT
              );
            }
            $prevPageQuery = $groups->getPrevPageQuery();
            $nextPageQuery = $groups->getNextPageQuery();
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