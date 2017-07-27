<?php
require_once 'config.php';
require_once '../vendor/autoload.php';
use Uploadcare\Api;
  
  $pageLimit = 10;
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
  
// Reading Post params
  $action = null;

  $actionParam = 'action';
  if(array_key_exists($actionParam, $_POST)) {
    $action = $_POST[$actionParam];
  }
  
  $dataParam = 'data';
  if(array_key_exists($dataParam, $_POST)) {
    $data = json_decode($_POST[$dataParam]);
  }
  
  if($action == 'store') {
    $processedFiles = $api->storeMultipleFiles($data);
  }
  if($action == 'delete') {
    $processedFiles = $api->deleteMultipleFiles($data);
  }

  $files = $api->getFileList(array(
    'limit' => $pageLimit,
    'from' => $from,
    'to' => $to,
    'removed' => false,
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
        <div class="row">
          <div class="col-md-12">
            <button class="btn btn-success" id="storeBtn">Store</button>
            <button class="btn btn-danger" id="deleteBtn">Delete</button>
          </div>
        </div>
        <form action="" method="POST" id="storeForm">
          <input type="hidden" name="action" value="store">
        </form>
        <form action="" method="POST" id="deleteForm">
          <input type="hidden" name="action" value="delete">
        </form>
        <table class="table table-striped" id="fileListTable">
          <thead>
            <tr>
              <th></th>
              <th>GUID</th>
              <th>File name</th>
              <th>Image</th>
              <th>Size</th>
              <th>State</th>
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
              $state = $data["datetime_removed"] ?
                '<span class="label label-danger">Deleted</span>' :
                  ($data["datetime_stored"] ?
                    '<span class="label label-success">Stored</span>' :
                    '<span class="label label-primary">Uploaded</span>');

              echo (<<<EOT
              <tr>
              <td>
                <input type="checkbox" data-fileId="${uuid}"/>
              </td>
              <td>${uuid}</td>
              <td>
                <a href="editFile.php?fileId=${uuid}">${fileName}</a>
              </td>
              <td><img src="${imageUrl}"/></td>
              <td>${size}</td>
              <td>${state}</td>
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
  <script type="text/javascript">
  $(function() {
    console.log('page loaded');
    var storeForm = $('#storeForm');
    var deleteForm = $('#deleteForm');
    var submitfunc = function() {
      var checkedData = $('input[type="checkbox"]:checked');
      var formData = [];
      checkedData.toArray().forEach(function(el) {
        formData.push($(el).attr('data-fileId'));
      });
      formData = JSON.stringify(formData);
      jQuery('<input/>', {
        type: 'hidden',
        name: 'data',
        value: formData
      }).appendTo(this);
      return true;
    };

    storeForm.submit(submitfunc);
    deleteForm.submit(submitfunc);

    $('#storeBtn').click(function(ev){
      storeForm.submit();
    });
    
    $('#deleteBtn').click(function(ev){
      deleteForm.submit();
    });
  });
  </script>
</body>
</html>
