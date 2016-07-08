<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';

$schoolHandler = SessionKeyHandler::get_from_session("school_handler", true);

$data_array['step'] = $_POST['step'];

switch ($_POST['step']) {
    case "1":
        if ($schoolHandler->create_school_step_one($_POST['school_name'], $_POST['school_phone'], $_POST['school_address'], $_POST['school_email'], (int) $_POST['school_type_id'])) {
            SessionKeyHandler::add_to_session("school_handler", $schoolHandler, true);
            $data_array[] = $schoolHandler->school;
            $data_array['status_value'] = true;
            
        } else {
            $data_array["status_value"] = false;
        }
        break;
    case "2":
        $format = "m-d-Y"; // Formatet skal være i det format inputtet viser. etc. 01/20/2016 vil være m-d-Y
        $d = date_parse_from_format($format, $_POST['school_subscription_end']);
        $d_string = $d['year'] . '/' . $d['month'] . '/' . $d['day'];
        if ($schoolHandler->create_school_step_two($schoolHandler->school, (int) $_POST['school_max_students'], $d_string)) {
            SessionKeyHandler::add_to_session("school_handler", $schoolHandler, true);
            $data_array["status_msg"] = "success";            
            $data_array['status_value'] = true;
        } else {
            $data_array['status_msg'] = $schoolHandler->error->title;
            $data_array["status_value"] = false;
        }
        break;
}
echo json_encode($data_array);
die();