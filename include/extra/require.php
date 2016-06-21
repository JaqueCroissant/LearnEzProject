<?php
    require_once "include/extra/db.class.php";
    require_once 'include/handler/errorHandler.php';
    require_once 'include/handler/dbHandler.php';
    require_once 'include/pages/pagelist.php';
    
    $dbHandler = new DbHandler($db_username, $db_password);
    
?>