<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/certificatesHandler.php';

$certificateHandler = new certificatesHandler();

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
                $jsonArray["status_value"] = false;
                $jsonArray["certificate"] = $certificateHandler->current_certificate;
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
