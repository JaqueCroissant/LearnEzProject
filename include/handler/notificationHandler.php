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
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead "
                        . "FROM user_notifications "
                        . "INNER JOIN notifications "
                        . "ON notifications.id = user_notifications.notification_id "
                        . "INNER JOIN users "
                        . "ON users.id = user_notifications.sender_user_id "
                        . "INNER JOIN translation_notifications "
                        . "ON translation_notifications.notification_id = notifications.id "
                        . "AND translation_notifications.language_id = "
                        . "(IF ((SELECT notifications.id FROM translation_notifications "
                        . "WHERE notification_id = notifications.id "
                        . "AND language_id = :languageId "
                        . "LIMIT 1),:languageId,notifications.default_language_id)) "
                        . "WHERE user_notifications.user_id = :userId "
                        . "ORDER BY user_notifications.is_read, user_notifications.datetime "
                        . "LIMIT :limit"
                        , TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $userId, $limit);
                echo TranslationHandler::get_current_language();
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
        /*
         * keys in the parameter array is below
         * sender_id
         * default_language_id
         * duration
         * notifications (array)
         *      notification.class
         *          title
         *          text
         *          language_id
         * reciever_ids (array)
         *      id
         */
        public function create_new_notification($parameters){
            try {
                $this->check_array($parameters);
                $this->check_notification($parameters["notifications"]);
                $this->check_recievers($parameters["reciever_ids"]);

                $notId = $this->create_notification($parameters["default_language_id"], $parameters["duration"]);
                foreach ($parameters["notifications"] as $notification) {
                    $this->create_notification_translation($notId, $notification->language_id, $notification->title, $notification->text);
                }
                foreach ($parameters["reciever_ids"] as $id) {
                    $this->create_user_notification($id, $parameters["sender_id"], $notId);
                }
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        private function create_notification_translation($notificationId, $languageId, $title, $text){        
            DbHandler::get_instance()->Query("INSERT INTO translation_notifications (notification_id, language_id, title, text) "
                    . "VALUES (:notIf,:langId,:title,:text)", $notificationId, $languageId, $title, $text);
        }
        
        private function create_user_notification($recieverId, $senderId, $notificationId){
            DbHandler::get_instance()->Query("INSERT INTO user_notifications (user_id, notification_id, datetime, is_read, sender_user_id) "
                    . "VALUES (:recieverId,:notId,NOW(),:isRead,:senderId)", $recieverId, $notificationId, 0, $senderId);
        }
        
        private function create_notification($defaultLandguageId, $duration, $prefix = null){
            DbHandler::get_instance()->Query("INSERT INTO notifications (default_language_id, prefix, duration) "
                    . "VALUES (:langId,:prefix,:duration)", $defaultLandguageId, $prefix, $duration);
            return DbHandler::get_instance()->last_inserted_id();
        }
        
        private function check_array($array){
            if (!array_key_exists("sender_id", $array) || 
                    !array_key_exists("default_language_id", $array) || 
                    !array_key_exists("duration", $array) || 
                    !array_key_exists("notifications", $array) ||
                    !array_key_exists("reciever_ids", $array)) {
                throw new Exception("NOTIFICATION_ARRAY_KEY_DOESNT_EXIST");
            }
            if (count($array["notifications"]) < 1) {
                throw new Exception("NOTIFICATION_NO_NOTIFICATIONS");
            }
            if (count($array["reciever_ids"]) < 1) {
                throw new Exception("NOTIFICATIONS_NO_RECIEVERS");
            }
        }
        
        private function check_notification($notifications){
            foreach ($notifications as $notification) {
                $this->check_title($notification->title);
                $this->is_null_or_empty($notification->text);
                $this->check_numeric($notification->language_id);
            }
        }
        
        private function check_prefix($prefix){
            if (isset($prefix) && empty($prefix)) {
                throw new Exception("NOTIFICATION_PREFIX_NOT_SET");
            }
        }
        
        private function check_duration($duration){
            $this->check_numeric($duration);
            if ($duration < 31536000) {
                throw new Exception("NOTIFICATION_DURATION_TOO_LONG");
            }
        }
        
        private function check_numeric($value){
            $this->is_null_or_empty($value);
            if (!is_numeric($value)) {
                throw new Exception("NOTIFICATION_FOREIGN_ID_NOT_INT");
            }
        }
        
        private function check_recievers($ids){
            foreach ($ids as $value) {
                $this->is_null_or_empty($value);
                if (!is_numeric($value)) {
                    throw new Exception("NOTIFICATION_INVALID_RECIEVER_ID");
                }
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
            if (!isset($value)) {
                throw new Exception("OBJECT_DOESNT_EXIST");
            }
            if (empty($value)) {
                throw new Exception("OBJECT_IS_EMPTY");
            }
        }
        
        
    }
?>

