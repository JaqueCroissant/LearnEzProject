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
    if ($notificationHandler->update_unseen_notification_count()) {
        $jsonArray["status_value"] = true;
        $jsonArray['count'] = $notificationHandler->get_unseen_notifications_count();
    }
    else {
        $jsonArray["status_value"] = false;
        $jsonArray["error"] = $notificationHandler->error->title;
    }
    echo json_encode($jsonArray);
}

function get_notifications(){
    $notificationHandler = new NotificationHandler();
    if (!$notificationHandler->load_notifications(0, 7)) {
        $json_array["status_value"] = false;
        $json_array["error"] = $notificationHandler->error->title;
    }
    else {
        $data = $notificationHandler->get_notifications();
        $json_array['notifications'] = "";     
        if (count($data) == 0) {
           $json_array["status_text"] = "<div style='text-align:center;font-style:italic;padding:6px 0 6px 0;width:100%;'>" . TranslationHandler::get_static_text("NO_NOTIFICATIONS") . "</div>";
        }
        else {
        $notificationHandler->load_arguments($data);
            foreach ($data as $value) {
                $args = $notificationHandler->get_arguments($value->arg_id);
                $text = $notificationHandler->parse_text($value->text, $args);
                if ($text[0]) {
                    $json_array['notifications'] .= notification_setup($value, $text, $args);
                }
            }
            if(count($data) < 7) {
                $json_array["status_text"] = "<div style='text-align:center;font-style:italic;padding:6px 0 6px 0;width:100%;'>" . TranslationHandler::get_static_text("NO_MORE_NOTIFICATIONS") . "</div>";
            }
        }
        $json_array["status_value"] = true;
        $json_array["translations"] = array("SEE_ALL" => TranslationHandler::get_static_text("SEE_ALL"), "NOTIFICATIONS" => TranslationHandler::get_static_text("NOTIFICATIONS"));
    }
    echo json_encode($json_array);
}

function get_more_notifications(){
    $notificationHandler = new NotificationHandler();
    if ($notificationHandler->load_notifications(isset($_POST['offset']) ? $_POST['offset'] : 0, 5)) {
        $json_array["notifications"] = "";
        $data = $notificationHandler->get_notifications();
        $notificationHandler->load_arguments($data);
        foreach ($data as $not) {
            $args = $notificationHandler->get_arguments($not->arg_id);
            $text = $notificationHandler->parse_text($not->text, $args);
            if ($text[0]) {
                $json_array['notifications'] .= notification_setup($not, $text, $args);
            }
        }
        if (count($data) != 5) {
            $json_array["status_text"] = "<div style='text-align:center;font-style:italic;padding:6px 0 6px 0;width:100%;'>" . TranslationHandler::get_static_text("NO_MORE_NOTIFICATIONS") . "</div>";
        }
        $json_array['error'] = (isset($notificationHandler->error) ? $notificationHandler->error->title : null);
        echo json_encode($json_array);
    }
}

function notification_setup($value, $text, $args){
    $time = time_elapsed($value->datetime);
    $final = "<div class='notification item_hover " . ($value->isRead == 2 ? "" : "item_unread")
            . "' style='width:100%;'><div class='cursor read_notif " . "' notif='" . $value->id . "' page='" . $value->link_page . "' id='" . $value->link_page . "' step='" . $value->link_step . "' args='" . $value->link_args . "" . (isset($args["link_id"]) ? $args["link_id"] : "") . "'>"
            . "<div class='notifcation_content notification_icon' style='width:8.33%'><div class='fa " . $value->icon . "' style='font-size:1.5em'></div></div>"
            . "<div class='fz-sm notifcation_content' style='padding-left:12px;width:80%;'><p class='mail-item-excerpt'>" . $text . "</p><i class='fz-sm' style='width:100% !important;'>" . $time["value"] . " " . TranslationHandler::get_static_text($time["prefix"]) . " " . TranslationHandler::get_static_text("DATE_AGO") . "</i></div></div>"
            . "<div class='notification_button notifcation_content' style='width:11.67%;'><div class='notification_delete cursor zmdi zmdi-close-circle' notif='" . $value->id . "'></div></div></div>";
    return $final;
}

function delete(){
    $notifs = isset($_POST["notifs"]) ? $_POST["notifs"] : array();
    $notificationHandler = new NotificationHandler();
    if($notificationHandler->delete_notifications($notifs)) {
        $json_array["status_value"] = true;
        $json_array["affected_notifs"] = $notifs;
    }
    else {
        $json_array["status_value"] = false;
        $json_array["error"] = $notificationHandler->error->title;
    }
    
    echo json_encode($json_array);
}

function read(){
    $notifs = isset($_POST["notifs"]) ? $_POST["notifs"] : null;
    
    if (isset($notifs)) {
        $notificationHandler = new NotificationHandler();
        if($notificationHandler->read_notifications($notifs)) {
            $json_array["status_value"] = true;
            $json_array["affected_notifs"] = $notifs;
        }
        else {
            $json_array["status_value"] = false;
            $json_array["error"] = $notificationHandler->error->title;
        }
    }
    else {
        $json_array["status_value"] = false;
        $json_array["error"] = ErrorHandler::return_error("DATABASE_UNKNOWN_ERROR");
    }
    echo json_encode($json_array);
}