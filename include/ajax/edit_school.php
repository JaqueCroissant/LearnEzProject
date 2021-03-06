<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/courseHandler.php';
$schoolHandler = new SchoolHandler();
$courseHandler = new CourseHandler();
$array = array();
$format = "Y-m-d";

if (isset($_POST['state'])) {
    switch ($_POST['state']) {
        case "update_school":
            $school_name = (isset($_POST['school_name']) ? $_POST['school_name'] : "");
            $school_address = (isset($_POST['school_address']) ? $_POST['school_address'] : "");
            $school_zip_code = (isset($_POST['school_zip_code']) ? $_POST['school_zip_code'] : "");
            $school_city = (isset($_POST['school_city']) ? $_POST['school_city'] : "");
            $school_phone = (isset($_POST['school_phone']) ? $_POST['school_phone'] : "");
            $school_email = (isset($_POST['school_email']) ? $_POST['school_email'] : "");
            $school_max_students = (isset($_POST['school_max_students']) ? $_POST['school_max_students'] : "");
            $school_subscription_start = (isset($_POST['school_subscription_start']) ? $_POST['school_subscription_start'] : "");
            $school_subscription_end = (isset($_POST['school_subscription_end']) ? $_POST['school_subscription_end'] : "");
            $school_type_id = (isset($_POST['school_type_id']) ? $_POST['school_type_id'] : "");
            $school_id = (isset($_POST['school_id']) ? $_POST['school_id'] : "");
            $school_courses = isset($_POST['selected']) ? $_POST['selected'] : [];

            if ($schoolHandler->update_school_by_id($school_id, $school_name, $school_phone, $school_address, $school_zip_code, $school_city, $school_email, $school_type_id, $school_max_students, $school_subscription_start, $school_subscription_end)) {
                if (count($school_courses) > 0) {
                    if (!$courseHandler->assign_school_course($school_courses, $school_id)) {
                        $array['error'] = $courseHandler->error->title;
                        $array['status_value'] = false;
                    }
                }
                $array['success'] = TranslationHandler::get_static_text("SCHOOL_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $schoolHandler->error->title;
                $array['status_value'] = false;
            }
            break;
        case 'set_availability':

            $school_id = (isset($_POST['school_id']) ? $_POST['school_id'] : "");

            if ($schoolHandler->update_open_state($school_id)) {
                $array['success'] = TranslationHandler::get_static_text("SCHOOL_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $schoolHandler->error->title;
                $array['status_value'] = false;
            }
            break;
    }
} else if (isset($_GET['school_id'])) {
    $school_id = (isset($_GET['school_id']) ? $_GET['school_id'] : "");

    if ($schoolHandler->get_school_by_id($school_id)) {
        $array['school'] = $schoolHandler->school;
        $array['status_value'] = true;
    } else {
        $array['error'] = $schoolHandler->error->title;
        $array['status_value'] = false;
    }
} else if (isset($_GET['state'])) {
    switch ($_GET['state']) {
        case 'set_availability':

            $school_id = (isset($_POST['school_id']) ? $_POST['school_id'] : "");

            if ($schoolHandler->update_open_state($school_id)) {
                $array['success'] = TranslationHandler::get_static_text("SCHOOL_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $schoolHandler->error->title;
                $array['status_value'] = false;
            }
            break;
    }
} else if(isset($_GET['delete_image'])){
    $school_id = isset($_GET['school']) ? $_GET['school'] : "";
    if ($schoolHandler->delete_image($school_id)) {
        $array['status_value'] = true;
        $array['success'] = TranslationHandler::get_static_text("IMAGE_DELETED");
    } else {
        $array['error'] = $schoolHandler->error->title;
        $array["status_value"] = false;
    }
}
echo json_encode($array);
die();
?>