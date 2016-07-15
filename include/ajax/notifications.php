<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/notificationHandler.php';


$action = (isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : null));
if ($action) {
    try {
        call_user_func($action);
    } catch (Exception $exc) {
        echo ErrorHandler::return_error($exc->getMessage());
    }
}

function get_new_notifications(){
    $notificationHandler = new NotificationHandler();
    if (SessionKeyHandler::session_exists("user") && $notificationHandler->update_unseen_notification_count(SessionKeyHandler::get_from_session("user", true)->id)) {
        $jsonArray['count'] = $notificationHandler->get_unseen_notifications_count();
        echo json_encode($jsonArray);
    }
}

function get_notifications(){
    $notificationHandler = new NotificationHandler();
    if ($notificationHandler->load_notifications(SessionKeyHandler::get_from_session("user", true)->id, 0, 7)) {
    }
    $data = $notificationHandler->get_notifications();
    $json_array['notifications'] = "";     
    if (count($data) == 0) {
       $json_array["status_text"] = "<div class='col-md-12' style='text-align:center;font-style:italic;padding:6px 0 6px 0;'>" . TranslationHandler::get_static_text("NO_NOTIFICATIONS") . "</div>";
    }
    else {
        foreach ($data as $value) {
            $json_array['notifications'] .= notification_setup($value);
        }
        if(count($data) < 7) {
            $json_array["status_text"] = "<div class='col-md-12' style='text-align:center;font-style:italic;padding:6px 0 6px 0;'>" . TranslationHandler::get_static_text("NO_MORE_NOTIFICATIONS") . "</div>";
        }
    }
    echo json_encode($json_array);
}

function get_more_notifications(){
    $notificationHandler = new NotificationHandler();
    if ($notificationHandler->load_notifications(SessionKeyHandler::get_from_session("user", true)->id, isset($_POST['offset']) ? $_POST['offset'] : 0, 5)) {
        $json_array["notifications"] = "";
        foreach ($notificationHandler->get_notifications() as $not) {
            $json_array["notifications"] .= notification_setup($not);
        }
        if (count($notificationHandler->get_notifications()) != 5) {
            $json_array["status_text"] = "<div class='col-md-12' style='text-align:center;font-style:italic;padding:6px 0 6px 0;'>" . TranslationHandler::get_static_text("NO_MORE_NOTIFICATIONS") . "</div>";
        }
        $json_array['error'] = $notificationHandler->error;
        echo json_encode($json_array);
    }
}

function notification_setup($value){
    $time = time_elapsed($value->datetime);
    $final = "<div class='notification col-md-12 " . ($value->isRead == 0 ? "notification_unseen" : ($value->isRead == 1 ? "notification_unread" : "notification_read"))
            . "'><div class='col-md-1 notifcation_content'><div class='fa " . $value->icon . "' style='font-size:1.5em'></div></div>"
            . "<div class='col-md-10 fz-sm notifcation_content'>" . $value->text . "<br/><i class='fz-sm' style='width:100% !important;'>" . $time["value"] . " " . TranslationHandler::get_static_text($time["prefix"]) . " " . TranslationHandler::get_static_text("DATE_AGO") . "</i></div>"
            . "<div class='col-md-1 notification_button notifcation_content zmdi zmdi-close-circle' notif='" . $value->id . "'></div></div>";
    return $final;
}

function delete(){
    $notifs = isset($_POST["notifications"]) ? $_POST["notifications"] : null;
    
    if (isset($notifs)) {
        $notificationHandler = new NotificationHandler();
        if($notificationHandler->delete_notifications($notifs)) {
            $json_array["status_value"] = true;
            $json_array["affected_notifs"] = $notifs;
        }
        else {
            $json_array["status_value"] = false;
            $json_array["error"] = $notificationHandler->error;
        }
    }
    else {
        $json_array["status_value"] = false;
        $json_array["error"] = ErrorHandler::return_error("DATABASE_UNKNOWN_ERROR");
    }
    echo json_encode($json_array);
}

function read(){
    $notifs = isset($_POST["notifications"]) ? $_POST["notifications"] : null;
    
    if (isset($notifs)) {
        $notificationHandler = new NotificationHandler();
        if($notificationHandler->read_notifications($notifs)) {
            $json_array["status_value"] = true;
            $json_array["affected_notifs"] = $notifs;
        }
        else {
            $json_array["status_value"] = false;
            $json_array["error"] = $notificationHandler->error;
        }
    }
    else {
        $json_array["status_value"] = false;
        $json_array["error"] = ErrorHandler::return_error("DATABASE_UNKNOWN_ERROR");
    }
    echo json_encode($json_array);
}