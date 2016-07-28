<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();

if(isset($_POST)) {
    $step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    switch($step) {
        case "create_course":
            $os_id = isset($_POST["os"]) ? $_POST["os"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $color = isset($_POST["color"]) ? $_POST["color"] : null;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_course($os_id, $points, $color, $sort_order, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("COURSE_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "create_lecture":
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_lecture($course_id, $points, $sort_order, $difficulty, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("LECTURE_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "create_test":
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_test($course_id, $points, $sort_order, $difficulty, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("TEST_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
    }
}

if(isset($_GET["get_lectures"]) && isset($_GET["course_id"])) {
    if($courseHandler->get_lectures($_GET["course_id"])) {
        $jsonArray["lectures"] = "";
        for($i = 0; $i < count($courseHandler->lectures); $i++) {
            $jsonArray["lectures"] .= '<option value="'.$courseHandler->lectures[$i]->sort_order.'">'.($i + 1).'. '.$courseHandler->lectures[$i]->title.'</option>';
        }
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["get_tests"]) && isset($_GET["course_id"])) {
    if($courseHandler->get_tests($_GET["course_id"])) {
        $jsonArray["tests"] = "";
        for($i = 0; $i < count($courseHandler->tests); $i++) {
            $jsonArray["tests"] .= '<option value="'.$courseHandler->tests[$i]->sort_order.'">'.($i + 1).'. '.$courseHandler->tests[$i]->title.'</option>';
        }
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}
    