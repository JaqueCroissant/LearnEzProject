<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = new SchoolHandler();
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

            if ($schoolHandler->update_school_by_id($school_id, $school_name, $school_phone, $school_address, $school_zip_code, $school_city, $school_email, $school_type_id, $school_max_students, $school_subscription_start, $school_subscription_end)) {
                $array['success'] = TranslationHandler::get_static_text("SCHOOL_UPDATED");
                $array['status_value'] = true;
            } else {
                $array['error'] = $schoolHandler->error->title;
                $array['status_value'] = false;
            }
            break;
        case "update_open_state":

            break;
    }
} elseif (isset($_GET['school_id'])) {
    $school_id = (isset($_GET['school_id']) ? $_GET['school_id'] : "");

    if ($schoolHandler->get_school_by_id($school_id)) {
        $array['school'] = $schoolHandler->school;
        $array['status_value'] = true;
    } else {
        $array['error'] = $schoolHandler->error->title;
        $array['status_value'] = false;
    }
}
else if(isset($_GET['state']))
{
    switch($_GET['state'])
    {
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
}
echo json_encode($array);
die();
?>