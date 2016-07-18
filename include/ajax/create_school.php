<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = new SchoolHandler();

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
        $format = "m-d-Y"; // Formatet skal være i det format inputtet viser. etc. 01/20/2016 vil være m-d-Y
        $d = date_parse_from_format($format, $_POST['school_subscription_end']);
        $d_string = $d['year'] . '/' . $d['month'] . '/' . $d['day'];
        $schoolHandler->school = new School();
        $schoolHandler->school->name = $_POST['name'];
        $schoolHandler->school->address = $_POST['address'];
        $schoolHandler->school->school_type_id = $_POST['school_type_id'];
        $schoolHandler->school->phone = $_POST['phone'];
        $schoolHandler->school->email = $_POST['email'];
        
        if ($schoolHandler->create_school_step_two($schoolHandler->school, (int) $_POST['school_max_students'], $d_string)) {
            $data_array["school"] = $schoolHandler->school;
            $data_array['success'] = TranslationHandler::get_static_text("SCHOOL_CREATED");
            $data_array['status_value'] = true;
        } else {
            $data_array['error'] = $schoolHandler->error->title;
            $data_array["status_value"] = false;
        }
        break;
}
echo json_encode($data_array);
die();