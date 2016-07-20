<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = new SchoolHandler();
$array = array();
$format = "Y-m-d";

if (isset($_POST['state'])) {

} elseif (isset($_GET['school_id'])) {
    if (!isset($_GET['school_id'])) {
        $school_id = "";
    } else {
        $school_id = $_GET['school_id'];
    }

    if ($schoolHandler->get_school_by_id($school_id)) {
        $array['class'] = $schoolHandler->school;
        $array['status_value'] = true;
    } else {
        $array['error'] = $schoolHandler->error->title;
        $array['status_value'] = false;
    }
}
echo json_encode($array);
die();
?>