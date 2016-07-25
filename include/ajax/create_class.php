<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';

$classHandler = new ClassHandler();
$class_begin = (isset($_POST['class_begin']) ? $_POST['class_begin'] : "");
$class_end = (isset($_POST['class_end']) ? $_POST['class_end'] : "");
$title = (isset($_POST['class_title']) ? $_POST['class_title'] : "");
$school_id = (isset($_POST['school_id']) ? $_POST['school_id'] : "");
$class_open = (isset($_POST['class_open']) && $_POST['class_open'] == "on" ? "1" : "0");
$class_description = (isset($_POST['class_description']) ? $_POST['class_description'] : "");

if ($classHandler->create_class($title, $school_id, $class_end, $class_description, $class_open, $class_begin)) {
    $data_array['success'] = TranslationHandler::get_static_text("CLASS_CREATED");
    $data_array['status_value'] = true;
} else {
    $data_array['error'] = $classHandler->error->title;
    $data_array["status_value"] = false;
}


echo json_encode($data_array);
die();