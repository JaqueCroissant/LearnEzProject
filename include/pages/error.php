<?php
require_once '../../include/ajax/require.php';
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
                    <h1 class="animated shake four_oh_four_title">404</h1>
                    <h5 class="animated slideInUp four_oh_four_msg"><?php echo TranslationHandler::get_static_text("ERROR_PAGE_NOT_FOUND"); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>