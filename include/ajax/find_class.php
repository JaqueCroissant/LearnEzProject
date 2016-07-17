<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
$array = array();
$format = "Y-m-d";


if (isset($_POST['state'])) {
    switch ($_POST['state']) {
        case "update_class":
            $class_end;
            $class_begin;
            if (!isset($_POST['class_open'])) {
                $array['error'] = "class_open_state is not set";
                $array["status_value"] = false;
                echo json_encode($array);
                die();
            } elseif ($_POST['class_open'] == "1") {
                $class_open = true;
            } else {
                $class_open = false;
            }

            if (!isset($_POST['class_title'])) {
                $array['error'] = "class title is not set";
                $array["status_value"] = false;
                echo json_encode($array);
                die();
            }

            if (!isset($_POST['class_end'])) {
                $array['error'] = "class end is not set";
                $array["status_value"] = false;
                echo json_encode($array);
                die();
            } else {
                $class_end = date_parse_from_format($format, $_POST['class_end']);
            }

            if (!isset($_POST['class_begin'])) {
                $array['error'] = "class begin is not set";
                $array["status_value"] = false;
                echo json_encode($array);
                die();
            } else {
                $class_begin = date_parse_from_format($format, $_POST['class_begin']);
            }

            if (!isset($_POST['class_id'])) {
                $array['error'] = "class id is not set";
                $array["status_value"] = false;
                echo json_encode($array);
                die();
            }

            if (!isset($_POST['class_description'])) {
                $class_desc = "";
            } else {
                $class_desc = $_POST['class_description'];
            }

            if ($classHandler->update_class((int) $_POST['class_id'], $_POST['class_title'], $class_desc, $class_open, $class_end, $class_begin)) {
                $array['status_value'] = true;
                echo json_encode($array);
                die();
            } else {
                $array['error'] = $classHandler->error->title;
                $array["status_value"] = false;
                echo json_encode($array);
                die();
            }
            break;
        case "update_open_state":
            if (isset($_POST['class_open'])) {
                if ($classHandler->update_class_open((int) $_POST['class_id'], (int) $_POST['class_open'])) {
                    $array["status_value"] = true;
                    echo json_encode($array);
                    die();
                } else {
                    $array['error'] = $classHandler->error->title;
                    $array['int'] = $_POST['class_open'];
                    $array["status_value"] = false;
                    echo json_encode($array);
                    die();
                }
            }
            break;
    }
}

