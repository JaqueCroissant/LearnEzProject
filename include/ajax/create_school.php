<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = new SchoolHandler();
$data_array = array();
$format = "Y-m-d";

try {
    if (isset($_POST['step'])) {
        switch ($_POST['step']) {
            case "1":
                if ($schoolHandler->create_school_step_one($_POST['school_name'], $_POST['school_phone'], $_POST['school_address'], $_POST['school_email'], (int) $_POST['school_type_id'])) {
                    $data_array["school"] = $schoolHandler->school;
                    $data_array['success'] = TranslationHandler::get_static_text("STEP_ONE_COMPLETED");
                    $data_array['status_value'] = true;
                } else {
                    $data_array["status_value"] = false;
                }
                break;
            case "2":
                $schoolHandler->school = new School();

                if (!isset($_POST['school_subscription_end'])) {
                    throw new Exception("SUBSCRIPTION_END_INVALID");
                } else {
                    $end_date = date_parse_from_format($format, $_POST['school_subscription_end']);
                }

                if (!isset($_POST['name'], $_POST['address'], $_POST['school_type_id'], $_POST['phone'], $_POST['email'])) {
                    throw new Exception("SCHOOL_OBJECT_IS_EMPTY");
                } else {
                    $schoolHandler->school->name = $_POST['name'];
                    $schoolHandler->school->address = $_POST['address'];
                    $schoolHandler->school->school_type_id = $_POST['school_type_id'];
                    $schoolHandler->school->phone = $_POST['phone'];
                    $schoolHandler->school->email = $_POST['email'];
                }
                if (!isset($_POST['school_max_students'])) {
                    throw new Exception("MAX_STUDENTS_HAS_INVALID_NUMBER");
                } else {
                    $max_students = (int) $_POST['school_max_students'];
                }

                if ($schoolHandler->create_school_step_two($schoolHandler->school, $max_students, $end_date)) {
                    $data_array["school"] = $schoolHandler->school;
                    $data_array['success'] = TranslationHandler::get_static_text("SCHOOL_CREATED");
                    $data_array['status_value'] = true;
                } else {
                    $data_array['error'] = $schoolHandler->error->title;
                    $data_array["status_value"] = false;
                }
                break;
        }
    } else {
        throw new Exception("STEP_NOT_SET");
    }
} catch (Exception $exc) {
    $array['error'] = $exc->getMessage();
    $array["status_value"] = false;
}
echo json_encode($data_array);
die();

