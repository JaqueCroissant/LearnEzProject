<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/rightsHandler.php';
require_once '../../include/handler/courseHandler.php';
$rightsHandler = new RightsHandler();
$courseHandler = new CourseHandler();
$schoolHandler = new SchoolHandler();
$data_array = array();

if (isset($_POST['step'])) {
    
    switch ($_POST['step']) {
        case "1":
            $name = isset($_POST['school_name']) ? $_POST["school_name"] : "";
            $phone = isset($_POST['school_phone']) ? $_POST["school_phone"] : "";
            $address = isset($_POST['school_address']) ? $_POST["school_address"] : "";
            $zip_code = isset($_POST['school_zip_code']) ? $_POST["school_zip_code"] : "";
            $city = isset($_POST['school_city']) ? $_POST["school_city"] : "";
            $email = isset($_POST['school_email']) ? $_POST["school_email"] : "";
            $school_type_id = isset($_POST['school_type_id']) ? $_POST["school_type_id"] : "";

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
            $name = isset($_POST['name']) ? $_POST["name"] : "";
            $phone = isset($_POST['phone']) ? $_POST["phone"] : "";
            $address = isset($_POST['address']) ? $_POST["address"] : "";
            $zip_code = isset($_POST['zip_code']) ? $_POST["zip_code"] : "";
            $city = isset($_POST['city']) ? $_POST["city"] : "";
            $email = isset($_POST['email']) ? $_POST["email"] : "";
            $school_type_id = isset($_POST['school_type_id']) ? $_POST["school_type_id"] : "";
            $start_date = isset($_POST['school_subscription_start']) ? $_POST["school_subscription_start"] : "";
            $end_date = isset($_POST['school_subscription_end']) ? $_POST["school_subscription_end"] : "";
            $max_students = isset($_POST['school_max_students']) ? $_POST["school_max_students"] : "";

            $schoolHandler->school->name = $name;
            $schoolHandler->school->address = $address;
            $schoolHandler->school->school_type_id = $school_type_id;
            $schoolHandler->school->phone = $phone;
            $schoolHandler->school->email = $email;
            $schoolHandler->school->zip_code = $zip_code;
            $schoolHandler->school->city = $city;

            if ($schoolHandler->create_school_step_two($schoolHandler->school, $max_students, $start_date, $end_date)) {
                $data_array["school"] = $schoolHandler->school;
                $rightsHandler->create_school_rights($schoolHandler->school->id);
                $data_array['success'] = TranslationHandler::get_static_text("SCHOOL_CREATED");
                $data_array['status_value'] = true;
            } else {
                $data_array['error'] = $schoolHandler->error->title;
                $data_array['school'] = $schoolHandler->school;
                $data_array["status_value"] = false;
            }
            break;
        case "3":
            $school_id = (isset($_POST['id']) ? $_POST['id'] : "");
            $course_ids = (isset($_POST['selected']) ? $_POST['selected'] : []);
            if ($courseHandler->assign_school_course($course_ids, $school_id)) {
                $data_array['success'] = TranslationHandler::get_static_text("COURSES_ASSIGNED");
                $data_array['status_value'] = true;
            } else {
                $data_array['error'] = $courseHandler->error->title;
                $data_array["status_value"] = false;
            }
            break;
        case "upload_school_image":
            $image_file = isset($_FILES['school_image']) ? $_FILES['school_image'] : "";
            $school_id = isset($_POST['school_id']) ? $_POST['school_id'] : "";
            if ($schoolHandler->upload_image($school_id, $image_file)) {
                $data_array['success'] = TranslationHandler::get_static_text("THUMBNAIL_UPLOADED");
                $data_array['file_name'] = $schoolHandler->current_file_name;
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
