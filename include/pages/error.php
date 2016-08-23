<?php
require_once 'require.php';
if(isset($_SESSION["temp_error"])) {
    $error_message = "<div style='margin-bottom:10px';>". TranslationHandler::get_static_text("OOPS_ERROR") ."</div>";
    $error_message .= $_SESSION["temp_error"];
    unset($_SESSION["temp_error"]);
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <header class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("ERROR"); ?></h4>
            </header>
            <hr class="widget-separator">
            <div class="widget-body">
                <div class="simple-page-wrap" style="margin-bottom:100px">
                    <?php if(!isset($error_message)) { ?>
                        <h1 class="animated shake four_oh_four_title"><?php echo ((isset($_GET["step"]) && $_GET["step"] == "PAGE_NO_RIGHTS") ? TranslationHandler::get_static_text("ERROR_403") : TranslationHandler::get_static_text("ERROR_404")) ?></h1>
                    <?php } else { ?>
                        <div class="error-sign animated shake">!</div>
                    <?php } ?>
                    <h5 class="animated slideInUp four_oh_four_msg"><?php echo (isset($error_message) ? $error_message : ((isset($_GET["step"]) && $_GET["step"] == "PAGE_NO_RIGHTS") ? TranslationHandler::get_static_text("ERROR_PAGE_NO_PERMISSION") : TranslationHandler::get_static_text("ERROR_PAGE_NOT_FOUND"))) ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>