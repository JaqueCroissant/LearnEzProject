<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();

$course_id = isset($_GET["course_id"]) ? $_GET["course_id"] : null;

if(!$courseHandler->get_multiple($course_id, "lecture") || !$courseHandler->get_multiple($course_id, "test")) {
    ErrorHandler::show_error_page();
    die();
}

echo "Available lectures:<br>";
foreach($courseHandler->lectures as $value) {
    echo $value->title . "<br>";
}

echo "<br><br>Available tests:<br>";
foreach($courseHandler->tests as $value) {
    echo $value->title . "<br>";
}

?>