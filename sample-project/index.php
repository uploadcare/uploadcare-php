<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
use Uploadcare\Api;
$uc_handler = new Api(UC_PUBLIC_KEY, UC_SECRET_KEY);
?>
<!DOCTYPE html>
<html>
<head>
<meta encoding='utf-8'>
<title>Uploadcare</title>
<?php echo $uc_handler->widget->getScriptTag(); ?>
<script type="text/javascript">
    $ = uploadcare.jQuery;
    $(function() {
        handleUCForm = function() {
            if (!$('#uc_form input[name=qs-file]').val()) {
                $('#uc_form_nofile_hint').slideDown();
                setTimeout('hideNoFileHint()', '1500');
                return false;
            }
        };
        hideNoFileHint = function() {
            $('#uc_form_nofile_hint').slideUp();
        }
        $('#uc_form').submit(handleUCForm);
    });
</script>
</head>
<body class='welcome quick_start docs'>
    <div class='wrap'>
        <div class='page-content-placeholder'></div>
        <div class='page-content'>
            <section class='content text-content' style="width: 100%;">
                <article class='content-block'>
                    <ul class="instructions" style="list-style-type: none;">
                        <li id="step1">
                            <div class="item-header" role="foldable-folder">
                                <h2 class="upload">Use Uploadcare widget to upload any image.</h2>
                            </div>
                            <div class="hinted">
                                <form method="POST" action="upload.php" id="uc_form">
                                    <?php echo $uc_handler->widget->getInputTag('qs-file', array('attr' => 1)); ?>
                                    <input type="submit" value="Save!" />
                                </form>
                                <p id="uc_form_nofile_hint"
                                    style="display: none; margin-top: 20px; color: #ff0033;">
                                    Please, upload any image using Uploadcare widget.
                                </p>
                            </div>
                        </li>
                    </ul>

                </article>
            </section>
        </div>
    </div>
</body>
</html>
