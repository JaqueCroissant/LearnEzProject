<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
$array = array();
$format = "Y-m-d";

try {
    if (isset($_POST['state'])) {
        switch ($_POST['state']) {
            case "update_class":
                $class_end;
                $class_begin;
                if (!isset($_POST['class_open'])) {
                    throw new Exception("class_open_state is not set");
                } elseif ($_POST['class_open'] == "1") {
                    $class_open = true;
                } else {
                    $class_open = false;
                }

                if (!isset($_POST['class_title'])) {
                    throw new Exception("class title is not set");
                }

                if (!isset($_POST['class_end'])) {
                    throw new Exception("class end is not set");
                } else {
                    $class_end = date_parse_from_format($format, $_POST['class_end']);
                }

                if (!isset($_POST['class_begin'])) {
                    throw new Exception("class begin is not set");
                } else {
                    $class_begin = date_parse_from_format($format, $_POST['class_begin']);
                }

                if (!isset($_POST['class_id'])) {
                    throw new Exception("class id is not set");
                }

                if (!isset($_POST['class_description'])) {
                    $class_desc = "";
                } else {
                    $class_desc = $_POST['class_description'];
                }
                
                if (!isset($_POST['school_id'])) {
                    throw new Exception ("School id is not set");
                }

                if ($classHandler->update_class((int) $_POST['class_id'], $_POST['class_title'], $class_desc, $class_open, $class_end, $class_begin, (int) $_POST['school_id'])) {
                    $array['success'] = TranslationHandler::get_static_text("CLASS_UPDATED");
                    $array['status_value'] = true;
                    echo json_encode($array);
                    die();
                } else {
                    throw new Exception($classHandler->error->title);
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
                        throw new Exception($classHandler->error->title);
                    }
                }
                break;
            case "delete_class":
                if (!isset($_POST['class_id'])) {
                    throw new Exception("class id is not set");
                }
                if (isset($_POST['delete_class']) && $_POST['delete_class'] == "1") {
                    if ($classHandler->delete_class_by_id((int)$_POST['class_id'])) {
                        $array['success'] = TranslationHandler::get_static_text("CLASS_DELETED");
                        $array["status_value"] = true;
                        echo json_encode($array);
                        die();
                    } else {
                        throw new Exception($classHandler->error->title);
                    }
                }
        }
    }

    if (!isset($_GET['class_id'])) {
        throw new Exception("class id is not set");
    } else {
        if ($classHandler->get_class_by_id((int) $_GET['class_id'])) {
            $array['class'] = $classHandler->school_class;
            $array['status_value'] = true;
            echo json_encode($array);
            die();
        } else {
            throw new Exception($classHandler->error->title);
        }
    }
} catch (Exception $exc) {
    $array['error'] = $exc->getMessage();
    $array["status_value"] = false;
    echo json_encode($array);
    die();
}

