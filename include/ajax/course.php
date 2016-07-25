<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();

if(isset($_POST)) {
    
    if($loginHandler->create_course($course)) {
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $loginHandler->error->title;
    }
    echo json_encode($jsonArray);
    die();
}
    