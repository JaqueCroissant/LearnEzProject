<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/homeworkHandler.php';

$homeworkHandler = new HomeworkHandler();

if(isset($_POST)) {
    $step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    switch($step) {
        case "create_homework":
            $description = isset($_POST["description"]) ? $_POST["description"] : null;
            $title = isset($_POST["title"]) ? $_POST["title"] : null;
            $color = isset($_POST["color"]) ? $_POST["color"] : null;
            $students = isset($_POST["students"]) ? $_POST["students"] : array();
            $classes = isset($_POST["classes"]) ? $_POST["classes"] : array();
            $date_expire = isset($_POST["date_expire"]) ? $_POST["date_expire"] : null;
            $lectures = isset($_POST["lecture"]) ? $_POST["lecture"] : array();
            $tests = isset($_POST["test"]) ? $_POST["test"] : array();
            if($homeworkHandler->create_homework($description, $title, $color, $classes, $date_expire, $lectures, $tests)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("HOMEWORK_ASSIGNED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $homeworkHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
    }
}

if(isset($_GET["delete_homework"]) && isset($_GET["homework_id"])) {
    if($homeworkHandler->delete_homework($_GET["homework_id"])) {
        $jsonArray["success"] = TranslationHandler::get_static_text("HOMEWORK_DELETED");
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $homeworkHandler->error->title;
    }
    echo json_encode($jsonArray);
}