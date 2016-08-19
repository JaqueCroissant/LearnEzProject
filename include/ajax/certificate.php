<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/certificatesHandler.php';

$certificateHandler = new CertificatesHandler();

if(isset($_POST)) {
    $step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    if ($step == "find_certificate") {
        $one = isset($_POST["1"]) ? $_POST["1"] : "";
        $two = isset($_POST["2"]) ? $_POST["2"] : "";
        $three = isset($_POST["3"]) ? $_POST["3"] : "";
        $four = isset($_POST["4"]) ? $_POST["4"] : "";
        $five = isset($_POST["5"]) ? $_POST["5"] : "";
        if ($certificateHandler->construct_code(array($one, $two, $three, $four, $five))) {
            if ($certificateHandler->get_from_code($certificateHandler->current_code)) {
                $date_to_string = time_elapsed($certificateHandler->current_certificate->completion_date);
                $jsonArray["status_value"] = true;
                $jsonArray["course_color"] = $certificateHandler->current_certificate->course_color;
                $jsonArray["course_image"] = $certificateHandler->current_certificate->course_image;
                $jsonArray["course_title"] = $certificateHandler->current_certificate->course_title;
                $jsonArray["course_description"] = $certificateHandler->current_certificate->course_description;
                $jsonArray["complete_date"] = $date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO");
                $jsonArray["user_firstname"] = $certificateHandler->current_certificate->user_firstname;
                $jsonArray["user_surname"] = $certificateHandler->current_certificate->user_surname;
                $jsonArray["done_by"] = TranslationHandler::get_static_text("DONE_BY");
            }
            else {
                $jsonArray["status_value"] = false;
                $jsonArray["error"] = $certificateHandler->error->title;
            }
        }
        else {
            $jsonArray["status_value"] = false;
            $jsonArray["error"] = $certificateHandler->error->title;
        }
        echo json_encode($jsonArray);
    }
    
}
