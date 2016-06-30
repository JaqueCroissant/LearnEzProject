<?php
    class NotificationHandler extends Handler
    {
        public function __construct() {
            parent::__construct();
        }
        
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
        
        public function create_notification($title, $text, $reciever_id, $sender_id){
            try {
                $this->check_title($title);
                $this->is_null_or_empty($text);
                $this->check_reciever($reciever_id);
                $this->check_sender($sender_id);
                
                DbHandler::get_instance()->Query("INSERT INTO notifications (title, text) VALUES (:title,:text)", $title, $text);
                DbHandler::get_instance()->Query("INSERT INTO user_notifications (user_id, notification_id, datetime, is_read, sender_user_id) " .
                        "VALUES (:userId, :notId, NOW(), :isRead, :senderId)", $reciever_id, DbHandler::get_instance()->last_inserted_id(), 0,  $sender_id);
                echo "success";
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
            }
        }
        
        private function check_reciever($value){
            $this->is_null_or_empty($value);
            if (!is_numeric($value)) {
                throw new Exception("NOTIFICATION_INVALID_RECIEVER_ID");
            }
        }
        private function check_sender($value){
            $this->is_null_or_empty($value);
            if (!is_numeric($value)) {
                throw new Exception("NOTIFICATION_INVALID_SENDER_ID");
            }
        }
                
        private function check_title($title){
            $this->is_null_or_empty($title);
            if (strlen($title) > 100) {
                throw new Exception("NOTIFICATION_TITLE_TOO_LONG");
            }
        }
        
        private function is_null_or_empty($value){
            if (!isset($value) || empty($value)) {
                throw new Exception("OBJECT_DOESNT_EXIST");
            }
        }
        
        
    }
?>

