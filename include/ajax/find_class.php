<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
$array = array();

if (isset($_POST['class_open'])) {
    if ($classHandler->update_class_open( (int)$_POST['class_id'], (int)$_POST['class_open'])) {
        $array["status_value"] = true;
    } else {
        $array['error'] = $classHandler->error->title;
        $array['int'] = $_POST['class_open'];
        $array["status_value"] = false;
    }
}


echo json_encode($array);
die();