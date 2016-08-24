<?php
    if(!isset($_COOKIE['cookie_terms']))
    {  
    ?>
    <div class="cookie_overlay" >
        <div style="margin-left: 10px; margin-right: 10px;">
            <span class="pull-right"><i style="cursor: pointer;" class="zmdi zmdi-hc-lg zmdi-close close_terms"></i></span>
            <h5><b><?=strtoupper(TranslationHandler::get_static_text("ATTENTION")) . "!"; ?></b></h5>
            <p class="cookie-text"><?=TranslationHandler::get_static_text("COOKIE_INFO")?></p>
        </div>    
    </div>
    <?php
    }
?>
