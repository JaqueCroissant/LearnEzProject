<?php
require_once 'require.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/classHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();

if(isset($_POST))
{
    $school_id = isset($_POST['school']) ? $_POST['school'] : "";
    $class_id = isset($_POST['class']) ? $_POST['class'] : "";
    $students_to_add = isset($_POST['students_to_add']) ? $_POST['students_to_add'] : array();
    
    $class_array = array();
    $class_array[] = $class_id;

    if($schoolHandler->school_has_classes($school_id, $class_array))
    {
       if($classHandler->remove_user_from_class($class_id) && $classHandler->add_user_to_class($students_to_add, $class_id))
       {
           $jsonArray['success'] = TranslationHandler::get_static_text("CLASS_UPDATED");
           $jsonArray['status_value'] = true;
       }
       else
       {
           $jsonArray['error'] = $classHandler->error->title;
           $jsonArray['status_value'] = false;
       }
    }
    else
    {
        $jsonArray['error'] = $schoolHandler->error->title;
        $jsonArray['status_value'] = false;
    }

    echo json_encode($jsonArray);
    die();

        
    
}
?>

