<?php
    class NotificationHandler extends Handler
    {
        private $_unseen_notifications;
        private $_unread_notifications;
        private $_notifications;
        private $_load_notifications = true;
        
        
        public function __construct() {
            parent::__construct();
            $this->_notifications = array();
        }
        
        public function update_seen_notification_count($userId){
            try {
                $this->check_numeric($userId);
                
                $newCounter = DbHandler::get_instance()->count_query("SELECT notification_id "
                        . "FROM user_notifications "
                        . "WHERE user_id=:userId "
                        . "AND is_read=:isRead", $userId, 0); 
                
                if ($newCounter == 0) {
                    return false;
                }
                $this->_unseen_notifications = $newCounter;
                $this->_load_notifications = true;
                
                return true;      
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        public function read_notification($notificationId, $userId){
            try {
                $this->check_numeric($notificationId);
                $this->check_numeric($userId);
                
                DbHandler::get_instance()->query("UPDATE user_notifications "
                        . "SET is_read=2 "
                        . "WHERE id=:notificationId "
                        . "AND user_id=:userId", $notificationId, $userId);               
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }        
        }
        
        public function seen_notification($notificationId, $userId){
            try {
                $this->check_numeric($notificationId);
                $this->check_numeric($userId);
                
                DbHandler::get_instance()->query("UPDATE user_notifications "
                        . "SET is_read=1 "
                        . "WHERE id=:notificationId "
                        . "AND user_id=:userId", $notificationId, $userId);               
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }     
        }
        
        public function seen_notifications($userId){
            try {
                $this->check_numeric($userId);
                
                DbHandler::get_instance()->query("UPDATE user_notifications "
                        . "SET is_read=1 "
                        . "WHERE is_read=0 "
                        . "AND user_id=:userId", $userId);              
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }          
        }
        
        public function get_notifications(){
            return $this->_notifications;
        }
        
        public function get_unseen_notifications_count(){
            return $this->_unseen_notifications;
        }
        
        public function load_notifications($userId, $offset, $limit = 5){
            try {
                $this->check_bool($offset);
                if (!$this->_load_notifications && !$offset) {
                    return false;
                }
                $this->_load_notifications = false;
                $this->check_numeric($userId);
                $this->check_numeric($limit);
                
                $offset_count = 0;
                
                if ($offset) {
                    $offset_count = count($this->_notifications);
                }
                
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead "
                        . "FROM user_notifications "
                        . "INNER JOIN notifications "
                        . "ON notifications.id = user_notifications.notification_id "
                        . "LEFT JOIN users "
                        . "ON users.id = user_notifications.sender_user_id "
                        . "INNER JOIN translation_notifications "
                        . "ON translation_notifications.notification_id = notifications.id "
                        . "AND translation_notifications.language_id = "
                        . "(IF ((SELECT notifications.id FROM translation_notifications "
                        . "WHERE notification_id = notifications.id "
                        . "AND language_id = :languageId "
                        . "LIMIT 1),:languageId,notifications.default_language_id)) "
                        . "WHERE user_notifications.user_id = :userId "
                        . "ORDER BY user_notifications.datetime DESC "
                        . "LIMIT :limit OFFSET :offset"
                        , TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $userId, $limit, $offset_count);
                if (count($dbData) == 0) {
                    $this->_notifications = array();
                    return true;
                }
                $fullArray = array();
                foreach ($dbData as $notification) {
                    array_push($fullArray, new Notification($notification));
                }
                $this->_notifications = $fullArray;
                $this->seen_notifications($userId);
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        /*
         * keys in the parameter array is below
         * default_language_id
         * prefix
         * duration
         * notifications (array)
         *      notification.class
         *          title
         *          text
         *          language_id
         * reciever_ids (array)
         *      id
         */
        public function create_new_static_notification($default_language, $prefix, $duration, $notifications){
            try {
                $this->check_numeric($default_language);
                $this->check_prefix($prefix);
                $this->check_duration($duration);
                $this->check_notifications($notifications);
                
                $notId = $this->create_notification($default_language, $duration, $prefix);
                foreach ($notifications as $notification) {
                    $this->create_notification_translation($notId, $notification->language_id, $notification->title, $notification->text);
                }
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        public function create_new_static_user_notification($reciever, $prefix){
            try {
                $this->check_numeric($reciever);
                $this->check_prefix($prefix);
                
                $this->create_static_user_notification($reciever, $prefix);
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        
        /*
         * required data in notifications and recievers
         * notifications (array)
         *      notification.class
         *          title
         *          text
         *          language_id
         * recievers (array)
         *      id
         */
        public function create_new_notification($default_language, $duration, $notifications, $recievers, $sender){
            try {
                $this->check_numeric($default_language);
                $this->check_duration($duration);
                $this->check_notifications($notifications);
                $this->check_recievers($recievers);
                $this->check_sender($sender);

                $notId = $this->create_notification($$default_language, $duration);
                foreach ($notifications as $notification) {
                    $this->create_notification_translation($notId, $notification->language_id, $notification->title, $notification->text);
                }
                foreach ($reciever_ids as $id) {
                    $this->create_user_notification($id, $sender, $notId);
                }
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        private function create_static_user_notification($reciever, $prefix){
            DbHandler::get_instance()->Query("INSERT INTO user_notifications (user_id, notification_id, datetime, is_read, sender_user_id) "
                    . "VALUES (:reciever,(SELECT id FROM notifications WHERE prefix=:prefix LIMIT 1),NOW(),:isRead,:sender)", $reciever, $prefix, 0, null);
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
        
        private function check_status($status){
            if (!is_int($status) || ($status != 0 && $status != 1)) {
                throw new Exception("NOTIFICATION_UNKNOWN_STATUS");
            }
        }
        
        private function check_notifications($notifications){
            if (count($notifications) == 0) {
                throw new Exception("NOTIFICATION_NO_NOTIFICATIONS");
            }
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
        
        private function check_bool($value){
            if (!is_bool($value)) {
                throw new Exception("NOTIFICATION_VALUE_NOT_BOOL");
            }
        }
        
        private function check_numeric($value){
            $this->is_null_or_empty($value);
            if (is_numeric($value) && !is_int((int)($value))) {
                throw new Exception("NOTIFICATION_FOREIGN_ID_NOT_INT");
            }
        }
        
        private function check_recievers($ids){
            if (count($ids) == 0) {
                throw new Exception("NOTIFICATION_NO_RECIEVERSs");
            }
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

