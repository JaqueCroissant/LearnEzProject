<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
$array = array();
$format = "Y-m-d";

if (isset($_POST['state'])) {
    switch ($_POST['state']) {
        case "update_class":
            if (!isset($_POST['class_open'])) {
                $class_open = "";
            } else {
                $class_open = $_POST['class_open'];
                $array['class_open'] = $class_open;
            }

            if (!isset($_POST['class_title'])) {
                $class_title = "";
            } else {
                $class_title = $_POST['class_title'];
            }

            if (!isset($_POST['class_end'])) {
                $class_end = "";
            } else {
                $class_end = date_parse_from_format($format, $_POST['class_end']);
            }

            if (!isset($_POST['class_begin'])) {
                $class_begin = "";
            } else {
                $class_begin = date_parse_from_format($format, $_POST['class_begin']);
            }

            if (!isset($_POST['class_id'])) {
                $class_id = "";
            } else {
                $class_id = $_POST['class_id'];
            }

            if (!isset($_POST['class_description'])) {
                $class_desc = "";
            } else {
                $class_desc = $_POST['class_description'];
            }

            if (!isset($_POST['school_id'])) {
                $school_id = "";
            } else {
                $school_id = $_POST['school_id'];
            }

            if ($classHandler->update_class($class_id, $class_title, $class_desc, $class_open, $class_end, $class_begin, $school_id)) {
                $array['success'] = TranslationHandler::get_static_text("CLASS_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }
            break;
        case "update_open_state":
            if (isset($_POST['class_open'])) {
                if ($classHandler->update_class_open((int) $_POST['class_id'], (int) $_POST['class_open'])) {
                    $array['success'] = TranslationHandler::get_static_text("CLASS_UPDATED");
                    $array["status_value"] = true;
                    echo json_encode($array);
                    die();
                } else {
                    $array['error'] = $classHandler->error->title;
                    $array['status_value'] = false;
                }
            }
            break;
    }
} elseif (isset($_GET['state'])) {
    if (!isset($_GET['class_id'])) {
        $class_id = "";
    } else {
        $class_id = $_GET['class_id'];
    }

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
            if ($classHandler->delete_class_by_id($class_id)) {
                $array['success'] = TranslationHandler::get_static_text("CLASS_DELETED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $classHandler->error->title;
                $array['status_value'] = false;
            }

            break;
    }
}
echo json_encode($array);
die();

