<?php
    //$dbHandler->Query("INSERT INTO `rights` (prefix, sort_order) VALUES (:prefix, :sort_order)", "HEJ", 5);
    //$dbHandler->Query("UPDATE `rights` SET prefix = :prefix, sort_order = :sort_order WHERE id = :id", "JAKOB", 10, 1);
    //echo $dbHandler->CountQuery("SELECT * FROM `rights` WHERE prefix = :prefix AND sort_order = :sort_order", "HEJ", 5);
    //require_once 'include/handler/sessionKeyHandler.php';
    
    $user = new User();
    $user->userTypeId = 1;

    SessionKeyHandler::AddToSession("user", $user);
    $rightsHandler->GetFromDatabase();
    echo count($_SESSION['rights']);
?>

