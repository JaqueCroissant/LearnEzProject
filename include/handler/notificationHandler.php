<?php
    class NotificationHandler
    {
        public function getNumberOfUnread($userId){
            if (is_numeric($userId)) {
                return DbHandler::getInstance()->CountQuery("SELECT notification_id FROM user_notifications WHERE user_id=:userId AND is_read=0", $userId);
            }
            return 0;
        }
        
        public function readNotification($notificationId){
            if (is_numeric($userId)) {
                DbHandler::getInstance()->Query("UPDATE user_notification SET is_read=1 WHERE id=:notificationId", $notificationId);               
            }            
        }
        
        public function getNotifications($userId, $limit = 5){
            if (is_numeric($userId) && is_int($limit)) {
                $dbData = DbHandler::getInstance()->ReturnQuery("SELECT user_notifications.id, user_notifications.datetime, "
                        . "user_notifications.is_read AS isRead, users.firstname AS sender_name, "
                        . "notifications.title, notifications.text "
                        . "FROM user_notifications "
                        . "INNER JOIN users "
                        . "ON users.id = user_notifications.sender_user_id "
                        . "INNER JOIN notifications "
                        . "ON notifications.id = user_notifications.notification_id "
                        . "WHERE user_notifications.user_id = :userId "
                        . "ORDER BY user_notifications.is_read, user_notifications.datetime DESC "
                        . "LIMIT :limit", $userId, $limit);
                if (count($dbData) == 0) {
                    return array();
                }
                if (count($dbData) == 1) {
                    return array(new Notification(reset($dbData)));
                }
                $array = array();
                foreach ($dbData as $notification) {
                    array_push($array, new Notification($notification));
                }
                return $array;
            }
            return array();
        }
        
        
    }
?>

