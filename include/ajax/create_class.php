<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';

$classHandler = new ClassHandler();
$format = "Y-m-d";
if (isset($_POST['class_begin'])) {
    $class_begin = date_parse_from_format($format, $_POST['class_begin']);
} else {
    $class_begin = null;
}
$class_end = date_parse_from_format($format, $_POST['class_end']);
$title = $_POST['class_title'];
$school_id = (int) $_POST['school_id'];
if (isset($_POST['class_open'])) {
    if ($_POST['class_open'] == "on") {
        $class_open = 1;
    } else {
        $class_open = 0;
    }
}
if (isset($_POST['class_description'])) {
    $class_description = $_POST['class_description'];
} else {
    $class_description = "";
}

if ($classHandler->create_class($title, $school_id, $class_end, $class_description, $class_open, $class_begin)) {
    $data_array['status_value'] = true;
} else {
    $data_array['error'] = $classHandler->error->title;
    $data_array["status_value"] = false;
}


echo json_encode($data_array);
die();