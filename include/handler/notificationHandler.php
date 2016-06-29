<?php
    class NotificationHandler
    {
        public function get_number_of_unseen($userId){
            if (is_numeric($userId)) {
                return DbHandler::get_instance()->count_query("SELECT notification_id FROM user_notifications WHERE user_id=:userId AND is_read=0", $userId);
            }
            return 0;
        }
        
        public function get_number_of_unread($userId){
            if (is_numeric($userId)) {
                return DbHandler::get_instance()->count_query("SELECT notification_id FROM user_notifications WHERE user_id=:userId AND is_read=1", $userId);
            }
            return 0;
        }
        
        public function read_notification($notificationId, $userId){
            if (is_numeric($notificationId) && is_numeric($userId)) {
                DbHandler::get_instance()->query("UPDATE user_notifications SET is_read=2 WHERE id=:notificationId AND user_id=:userId", $notificationId, $userId);               
            }            
        }
        
        public function seen_notification($notificationId, $userId){
            if (is_numeric($notificationId) && is_numeric($userId)) {
                DbHandler::get_instance()->query("UPDATE user_notifications SET is_read=1 WHERE id=:notificationId AND user_id=:userId", $notificationId, $userId);               
            }            
        }
        
        public function seen_notifications($userId){
            if (is_numeric($userId)) {
                DbHandler::get_instance()->query("UPDATE user_notifications SET is_read=1 WHERE is_read=0 AND user_id=:userId", $userId);               
            }            
        }
        
        public function get_notifications($userId, $see = false, $limit = 5){
            if (is_numeric($userId) && is_int($limit)) {
                $dbData = DbHandler::get_instance()->return_query("SELECT user_notifications.id, user_notifications.datetime, "
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
                    return array(array(), array(), array());
                }
                $fullArray = array();
                if (count($dbData) == 1) {
                    $fullArray = array(new Notification(reset($dbData)));
                }
                else{
                    foreach ($dbData as $notification) {
                        array_push($fullArray, new Notification($notification));
                    }
                }
                $arr1 = array_filter($fullArray, function($value){return ($value->isRead == 0 ? true : false);});
                $arr2 = array_filter($fullArray, function($value){return ($value->isRead == 1 ? true : false);});
                $arr3 = array_filter($fullArray, function($value){return ($value->isRead == 2 ? true : false);});
                if ($see && count($arr1) > 0) {
                    $this->seen_notifications($userId);
                }
                return array($arr1, $arr2, $arr3);
            }
            return array(array(), array(), array());
        }
        
        
    }
?>

