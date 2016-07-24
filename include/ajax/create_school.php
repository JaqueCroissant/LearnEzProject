<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/rightsHandler.php';
$rightsHandler = new RightsHandler();
$schoolHandler = new SchoolHandler();

$format = "Y-m-d";

if (isset($_POST['step'])) {
    switch ($_POST['step']) {
        case "1":
            $data_array = array();
            if (isset($_POST['school_name'])) {
                $name = $_POST['school_name'];
            } else {
                $name = "";
            }
            if (isset($_POST['school_phone'])) {
                $phone = $_POST['school_phone'];
            } else {
                $phone = "";
            }
            if (isset($_POST['school_address'])) {
                $address = $_POST['school_address'];
            } else {
                $address = "";
            }
            if (isset($_POST['school_zip_code'])) {
                $zip_code = $_POST['school_zip_code'];
            } else {
                $zip_code = "";
            }
            if (isset($_POST['school_city'])) {
                $city = $_POST['school_city'];
            } else {
                $city = "";
            }
            if (isset($_POST['school_email'])) {
                $email = $_POST['school_email'];
            } else {
                $email = "";
            }
            if (isset($_POST['school_type_id'])) {
                $school_type_id = $_POST['school_type_id'];
            } else {
                $school_type_id = "";
            }

            if ($schoolHandler->create_school_step_one($name, $phone, $address, $zip_code, $city, $email, $school_type_id)) {
                $data_array["school"] = $schoolHandler->school;
                $data_array['success'] = TranslationHandler::get_static_text("STEP_ONE_COMPLETED");
                $data_array['status_value'] = true;
            } else {
                $data_array['error'] = $schoolHandler->error->title;
                $data_array["status_value"] = false;
            }
            break;
        case "2":
            $schoolHandler->school = new School();
            $data_array = array();
            if (isset($_POST['school_subscription_start'])) {
                $start_date = date_parse_from_format($format, $_POST['school_subscription_start']);
            } else {
                $start_date = "";
            }

            if (isset($_POST['school_subscription_end'])) {
                $end_date = date_parse_from_format($format, $_POST['school_subscription_end']);
            } else {
                $end_date = "";
            }

            if (isset($_POST['name'], $_POST['address'], $_POST['school_type_id'], $_POST['phone'], $_POST['email'], $_POST['zip_code'], $_POST['city'])) {
                $schoolHandler->school->name = $_POST['name'];
                $schoolHandler->school->address = $_POST['address'];
                $schoolHandler->school->school_type_id = $_POST['school_type_id'];
                $schoolHandler->school->phone = $_POST['phone'];
                $schoolHandler->school->email = $_POST['email'];
                $schoolHandler->school->zip_code = $_POST['zip_code'];
                $schoolHandler->school->city = $_POST['city'];
            } else {
                $schoolHandler->school->name = "";
                $schoolHandler->school->address = "";
                $schoolHandler->school->school_type_id = "";
                $schoolHandler->school->phone = "";
                $schoolHandler->school->email = "";
                $schoolHandler->school->zip_code = "";
                $schoolHandler->school->city = "";
            }

            if (isset($_POST['school_max_students'])) {
                $max_students = $_POST['school_max_students'];
            } else {
                $max_students = "";
            }

            
            if ($schoolHandler->create_school_step_two($schoolHandler->school, $max_students, $start_date, $end_date)) {
                $data_array["school"] = $schoolHandler->school;
                $rightsHandler->create_school_rights($schoolHandler->school->id);
                $data_array['success'] = TranslationHandler::get_static_text("SCHOOL_CREATED");
                $data_array['status_value'] = true;
            } else {
                $data_array['error'] = $schoolHandler->error->title;
                $data_array["status_value"] = false;
            }
            break;
    }
}
echo json_encode($data_array);
die();
