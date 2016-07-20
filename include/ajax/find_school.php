<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = new SchoolHandler();
$array = array();
$format = "Y-m-d";

if (isset($_POST['state'])) {
    switch ($_POST['state']) {
        case "update_school":
            if (!isset($_POST['school_name'])) {
                $school_name = "";
            } else {
                $school_name = $_POST['school_name'];
            }

            if (!isset($_POST['school_address'])) {
                $school_address = "";
            } else {
                $school_address = $_POST['school_address'];
            }

            if (!isset($_POST['school_zip_code'])) {
                $school_zip_code = "";
            } else {
                $school_zip_code = $_POST['school_zip_code'];
            }

            if (!isset($_POST['school_city'])) {
                $school_city = "";
            } else {
                $school_city = $_POST['school_city'];
            }

            if (!isset($_POST['school_phone'])) {
                $school_phone = "";
            } else {
                $school_phone = $_POST['school_phone'];
            }

            if (!isset($_POST['school_email'])) {
                $school_email = "";
            } else {
                $school_email = $_POST['school_email'];
            }

            if (!isset($_POST['school_max_students'])) {
                $school_max_students = "";
            } else {
                $school_max_students = $_POST['school_max_students'];
            }

            if (!isset($_POST['school_subscription_start'])) {
                $school_subscription_start = "";
            } else {
                $school_subscription_start = $_POST['school_subscription_start'];
            }

            if (!isset($_POST['school_subscription_end'])) {
                $school_subscription_end = "";
            } else {
                $school_subscription_end = $_POST['school_subscription_end'];
            }

            if (!isset($_POST['school_type_id'])) {
                $school_type_id = "";
            } else {
                $school_type_id = $_POST['school_type_id'];
            }

            if (!isset($_POST['school_id'])) {
                $school_id = "";
            } else {
                $school_id = $_POST['school_id'];
            }

            if ($schoolHandler->update_school_by_id($school_id, $school_name, $school_phone, $school_address, $school_zip_code, $school_city, $school_email, $school_type_id, $school_max_students, $school_subscription_start, $school_subscription_end)) {
                $array['success'] = TranslationHandler::get_static_text("SCHOOL_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $schoolHandler->error->title;
                $array['status_value'] = false;
            }
            break;
    }
} elseif (isset($_GET['school_id'])) {
    if (!isset($_GET['school_id'])) {
        $school_id = "";
    } else {
        $school_id = $_GET['school_id'];
    }

    if ($schoolHandler->get_school_by_id($school_id)) {
        $array['school'] = $schoolHandler->school;
        $array['status_value'] = true;
    } else {
        $array['error'] = $schoolHandler->error->title;
        $array['status_value'] = false;
    }
}
echo json_encode($array);
die();
?>