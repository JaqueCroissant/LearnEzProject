<?php
include 'ajax_require.php';

if (isset($_POST["action"])) {
    try {
        call_user_func($_POST["action"]);
    } catch (Exception $ex) {
        //TODO add error handling
    }

}

function getNotifications(){
    $notificationHandler = new NotificationHandler();
    $data = $notificationHandler->getNotifications(1, 5);
    echo "<div id='notificationData'>Nye" .
            "<div class='notificationGroup'>";     
    if (count($data[0]) == 0) {
        echo "Ingen nye notifikationer<br/><br/>";
    }
    else {
        foreach ($data[0] as $value) {
            echo "<a href=''><div class='notification'>" .
            $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . "<br/>" . 
            $value->text . "</div></a>";
        }
    }
    echo "</div>Gamle" .
            "<div class='notificationGroup'><br/>";
    if (count($data[1]) == 0) {
        echo "Ingen gamle notifikationer<br/><br/>";
    }
    else {
        foreach ($data[1] as $value) {
            echo $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . "<br/>" . 
            $value->text . "<br/><br/>";
        }
    }
}