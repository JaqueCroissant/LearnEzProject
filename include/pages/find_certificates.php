<?php
require_once 'require.php';
    
?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("FIND_CERTIFICATE"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body center">
                <form method="POST" id="find_certificate" url="certificate.php?step=find_certificate" name="find_certificate">
                    <input type="text" name="1" class="certificate_input form-control">
                    <span class="certificate_input_span">-</span>
                    <input type="text" name="2" class="certificate_input form-control">
                    <span class="certificate_input_span">-</span>
                    <input type="text" name="3" class="certificate_input form-control">
                    <span class="certificate_input_span">-</span>
                    <input type="text" name="4" class="certificate_input form-control">
                    <span class="certificate_input_span">-</span>
                    <input type="text" name="5" class="certificate_input form-control" maxlength="4">
                    <input type="button" name="submit" value="Find" class="certificate_submit btn btn-default m-l-sm" style="vertical-align:initial !important;">
                </form>
            </div>
        </div>
    </div>
</div>