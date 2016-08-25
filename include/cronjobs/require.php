<?php

    if(php_sapi_name() != "cli") {
        die("Direct access not permitted");
    }

    require_once '../extra/db.class.php';
    require_once '../class/error.class.php';
    require_once '../handler/errorHandler.php';
    require_once '../handler/translationHandler.php';
    require_once '../handler/sessionKeyHandler.php';
    require_once '../handler/dbHandler.php';

?>