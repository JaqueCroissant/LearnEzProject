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
    if (SessionKeyHandler::session_exists("user") && $notificationHandler->update_seen_notification_count(SessionKeyHandler::get_from_session("user", true)->id)) {
        SessionKeyHandler::add_to_session("notification_handler", $notificationHandler, true);
        $jsonArray['count'] = $notificationHandler->get_unseen_notifications_count();
        echo json_encode($jsonArray);
    }
}

function get_notifications(){
    $notificationHandler = SessionKeyHandler::get_from_session("notification_handler", true);
    if ($notificationHandler->load_notifications(SessionKeyHandler::get_from_session("user", true)->id, false, 5)) {
        SessionKeyHandler::add_to_session("notification_handler", $notificationHandler, true);
    }
    $data = $notificationHandler->get_notifications();
    $json_array['notifications'] = "Notifikationer<input type='button' value='X' id='notification_read_all'>";     
    if (count($data) == 0) {
       $json_array['notifications'] .= "<i>Ingen notifikationer</i><br/><br/>";
    }
    else {
        foreach ($data as $value) {
            $json_array['notifications'] .= notification_setup($value);
        }
    }
    echo json_encode($json_array);
}

function get_more_notifications(){
    $notificationHandler = SessionKeyHandler::get_from_session("notification_handler", true);
    if ($notificationHandler->load_notifications(SessionKeyHandler::get_from_session("user", true)->id, true, 5)) {
        SessionKeyHandler::add_to_session("notification_handler", $notificationHandler, true);
        $json_array["notifications"] = "";
        foreach ($notificationHandler->get_notifications() as $not) {
            $json_array["notifications"] .= notification_setup($not);
        }
        if (count($notificationHandler->get_notifications()) != 5) {
            $json_array["notifications"] .= "Ikke flere notifikationer";
            $json_array["status_value"] = true;
        }
        $json_array['error'] = $notificationHandler->error;
        echo json_encode($json_array);
    }
}

function notification_setup($value){
    $final = "";
    if ($value->isRead == 0) {$final .= "<div class='notification notification_unseen'>";}
    else {$final .= "<div class='notification notification_seen'>";}
    $final .= "<div class='notification_text pull-left'>" . $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . "<br/>" . 
    $value->text . "</div><div class='notification_button pull-right'><input type='button' value='X'></div></div>";
    return $final;
}