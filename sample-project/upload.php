<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
use Uploadcare\Api;

$file_id = $_POST['qs-file'];
$uc_handler = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
$file = $uc_handler->getFile($file_id);

try {
  $file->store();
} catch (Exception $e) {
  echo $e->getMessage()."<br />";
  echo nl2br($e->getTraceAsString());
  die();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta encoding='utf-8'>
<title>Uploadcare</title>
<body class='welcome quick_start  docs'>
	<div class='wrap'>
		<div class='page-content-placeholder'></div>
		<div class='page-content'>
			<section class='content text-content' style="width: 100%;">
				<article class='content-block'>
					<ul class="instructions" style="list-style-type: none;">
						<li id="step1">
							<div class="item-header" role="foldable-folder">
								<h2 class="upload">Here is a cropped image size 400x400. Click
									cropped image to see original one.</h2>
								<p>
									Would you like to <a href="../sample-project">upload more</a>?
								</p>
							</div>
							<div class="hinted">
								<a href="<?php echo $file->getUrl(); ?>" target="_blank"><img
									src="<?php print $file->resize(400, 400)->getUrl(); ?>" /> </a>
							</div>
						</li>
					</ul>

				</article>
			</section>
		</div>
	</div>
</body>
</html>
