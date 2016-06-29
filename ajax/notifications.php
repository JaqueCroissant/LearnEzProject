<?php
include 'ajax_require.php';

if (isset($_POST["action"])) {
    try {
        call_user_func($_POST["action"]);
    } catch (Exception $ex) {
        //TODO add error handling
    }

}

function get_new_notifications(){
    $notificationHandler = new NotificationHandler();
    if (SessionKeyHandler::session_exists("user")) {
        $jsonArray = [
        'id' => $notificationHandler->get_number_of_unseen(SessionKeyHandler::get_from_session("user", true)->id),
        ];
        echo json_encode($jsonArray);
    }
}

function get_notifications(){
    $notificationHandler = new NotificationHandler();
    $data = $notificationHandler->get_notifications(SessionKeyHandler::get_from_session("user", true)->id, true, 5);
    echo "<div id='notificationData'>Nye" .
            "<div class='notificationGroup'><br/>";     
    if (count($data[0]) == 0 && count($data[1]) == 0) {
        echo "<i>Ingen nye notifikationer</i><br/><br/>";
    }
    else {
        foreach ($data[0] as $value) {
            echo "<div class='notification notificationUnseen'>" .
            $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . "<br/>" . 
            $value->text . "</div>";
        }
    }
    if (!count($data[1]) == 0) {
        foreach ($data[1] as $value) {
            echo "<div class='notification notificationSeen'>" .
            $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . "<br/>" . 
            $value->text . "</div>";
        }
    }
    echo "</div>Gamle" .
            "<div class='notificationGroup'><br/>";
    if (count($data[2]) == 0) {
        echo "<i>Ingen gamle notifikationer</i><br/><br/>";
    }
    else {
        foreach ($data[2] as $value) {
            echo "<div class='notification notificationRead'>" .
            $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . "<br/>" . 
            $value->text . "</div>";
        }
    }
}