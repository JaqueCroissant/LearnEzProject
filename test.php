<?php
    //$dbHandler->Query("INSERT INTO `rights` (prefix, sort_order) VALUES (:prefix, :sort_order)", "HEJ", 5);
    //$dbHandler->Query("UPDATE `rights` SET prefix = :prefix, sort_order = :sort_order WHERE id = :id", "JAKOB", 10, 1);
<<<<<<< HEAD
    //print_r(DbHandler::getInstance()->ReturnQuery("SELECT * FROM `rights` WHERE prefix = :prefix AND sort_order = :sort_order", "HEJ", 5));
=======
<<<<<<< HEAD
    //echo $dbHandler->CountQuery("SELECT * FROM `rights` WHERE prefix = :prefix AND sort_order = :sort_order", "HEJ", 5);
    //require_once 'include/handler/sessionKeyHandler.php';
    
    $user = new User();
    $user->userTypeId = 1;

    SessionKeyHandler::AddToSession("user", $user);
    $rightsHandler->GetFromDatabase();
    echo count($_SESSION['rights']);
=======
    //print_r($dbHandler->ReturnQuery("SELECT * FROM `rights` WHERE prefix = :prefix AND sort_order = :sort_order", "HEJ", 5));
>>>>>>> 3fbe7e7565457bd1fc8277c794fac4b255e4b202
>>>>>>> bdaa06286f85a2b8c357368d8393e71976c2bbd6
?>

