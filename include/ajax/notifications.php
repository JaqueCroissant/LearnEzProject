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
    if ($notificationHandler->load_notifications(SessionKeyHandler::get_from_session("user", true)->id, 0, 5)) {
    }
    $data = $notificationHandler->get_notifications();
    $json_array['notifications'] = "Notifikationer<a href='javascript:void(0)' class='change_page notification_load_window' page='notifications' id='notifications' step='all'>Se alle</a>";     
    if (count($data) == 0) {
       $json_array['notifications'] .= "<i>Ingen notifikationer</i><br/><br/>";
       $json_array["status_value"] = true;
    }
    else {
        foreach ($data as $value) {
            $json_array['notifications'] .= notification_setup($value);
        }
        $json_array["status_value"] = false;
        if(count($data) < 5) {
            $json_array['notifications'] .= "Ikke flere notifikationer";
            $json_array["status_value"] = true;
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
            $json_array["notifications"] .= "Ikke flere notifikationer";
            $json_array["status_value"] = true;
        }
        $json_array['error'] = $notificationHandler->error;
        echo json_encode($json_array);
    }
}

function notification_setup($value){
    $final = "";
    if ($value->isRead != 2) {$final .= "<div class='notification notification_unread'>";}
    else {$final .= "<div class='notification notification_read'>";}
    $final .= "<div class='notification_icon pull-left'><img src='" . $value->icon . "' alt='missing' /></div>"
            . "<div class='notification_button pull-right'><input type='button' notif='" . $value->id . "' class='read_notification' value='X'></div>"
            . "<div class='notification_text pull-right'>" . $value->text . "<br/><i>" . $value->datetime . "</i></div></div>";
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