<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/notificationHandler.php';


if (isset($_POST["action"])) {
    try {
        call_user_func($_POST["action"]);
    } catch (Exception $exc) {
        echo ErrorHandler::return_error($exc->getMessage());
    }
}

function get_new_notifications(){
    $notificationHandler = SessionKeyHandler::get_from_session("notification_handler", true);
    if (SessionKeyHandler::session_exists("user") && $notificationHandler->update_notification_count(SessionKeyHandler::get_from_session("user", true)->id, 0)) {
        SessionKeyHandler::add_to_session("notification_handler", $notificationHandler, true);
        $jsonArray['count'] = $notificationHandler->get_unseen_notifications_count();
        echo json_encode($jsonArray);
    }
}

function get_notifications(){
    $notificationHandler = SessionKeyHandler::get_from_session("notification_handler", true);
    $update_session = false;
    if ($notificationHandler->load_notifications(SessionKeyHandler::get_from_session("user", true)->id, 5)) {
        $update_session = true;
    }
    
    $data = $notificationHandler->get_notifications();
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
    
    $notificationHandler->seen_notifications(SessionKeyHandler::get_from_session("user", true)->id);
    
    if ($update_session) {
        SessionKeyHandler::add_to_session("notification_handler", $notificationHandler, true);
    }
}