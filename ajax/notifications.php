<?php
include 'ajax_require.php';
require_once '../include/handler/notificationHandler.php';
require_once '../include/class/orm.class.php';
require_once '../include/class/notification.class.php';

$notificationHandler = new NotificationHandler();
$data = $notificationHandler->getNotifications(1, 5);
foreach ($data as $value) {
    echo $value->title . " | " . date("M-d H:i:s", strtotime($value->datetime)) . " | " . ($value->isRead == 0 ? "ulæst" : "læst") . "<br/>";
    echo $value->text . "<br/><br/>";
}