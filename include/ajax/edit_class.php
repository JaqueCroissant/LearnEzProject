<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
$array = array();
$format = "Y-m-d";

if (isset($_POST['state'])) {
    switch ($_POST['state']) {
        case "update_class":
            $class_open = (isset($_POST['class_open']) ? $_POST['class_open'] : "");
            $class_title = (isset($_POST['class_title']) ? $_POST['class_title'] : "");
            $class_end = (isset($_POST['class_end']) ? $_POST['class_end'] : "");
            $class_begin = (isset($_POST['class_begin']) ? $_POST['class_begin'] : "");
            $class_id = (isset($_POST['class_id']) ? $_POST['class_id'] : "");
            $class_desc = (isset($_POST['class_description']) ? $_POST['class_description'] : "");
            $school_id = (isset($_POST['school_id']) ? $_POST['school_id'] : "");
            $array['school_id'] = $school_id;
            $array['class_open'] = $class_open;
            $array['class_id'] = $class_id;
            if ($classHandler->update_class($class_id, $class_title, $class_desc, $class_open, $class_end, $class_begin, $school_id)) {
                $array['success'] = TranslationHandler::get_static_text("CLASS_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }
            break;
        case "delete_class":
            $class_id = (isset($_POST['class_id']) ? $_POST['class_id'] : "");
            if ($classHandler->delete_class_by_id($class_id)) {
                $array['success'] = TranslationHandler::get_static_text("CLASS_DELETED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }
            
            break;
    }
} elseif (isset($_GET['state'])) {
    $class_id = (isset($_GET['class_id']) ? $_GET['class_id'] : "");

    switch ($_GET['state']) {
        case "get_class":
            if ($classHandler->get_class_by_id($class_id)) {
                $array['class'] = $classHandler->school_class;
                $array['status_value'] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }
            break;

        case "delete_class":
            $class_id = (isset($_POST['class_id']) ? $_POST['class_id'] : "");
            if ($classHandler->delete_class_by_id($class_id)) {
                $array['success'] = TranslationHandler::get_static_text("CLASS_DELETED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }

            break;

        case "set_availability":

            if ($classHandler->update_class_open((int) $_POST['class_id'])) {
                $array['success'] = TranslationHandler::get_static_text("CLASS_UPDATED");
                $array["status_value"] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }

            break;
    }
}
echo json_encode($array);
die();

