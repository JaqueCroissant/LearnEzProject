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
        
        public function get_notification_categories(){
            try {
                return DbHandler::get_instance()->return_query("SELECT notifications_category.icon_class, "
                        . "notifications_category.category_name, "
                        . "translation_notifications_category.name "
                        . "FROM notifications_category "
                        . "INNER JOIN translation_notifications_category "
                        . "ON translation_notifications_category.notifications_category_id = notifications_category.id "
                        . "AND translation_notifications_category.translation_language_id = :langId", TranslationHandler::get_current_language());
                
            } catch (Exception $exc) {
                $this->error = $exc->getMessage();
            }
        }
        
        public function update_unseen_notification_count($userId){
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
        
        public function delete_notification($notificationId, $userId){
            try {
                $this->check_numeric($notificationId);
                $this->check_numeric($userId);
                
                DbHandler::get_instance()->query("DELETE FROM user_notifications "
                        . "WHERE id=:notificationId "
                        . "AND user_id=:userId", $notificationId, $userId);
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
                $this->check_numeric($offset);
                $this->check_numeric($userId);
                $this->check_numeric($limit);
                
                $langId = TranslationHandler::get_current_language();
                
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.id AS id, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead, "
                        . "notifications_category.icon_class AS icon, "
                        . "notifications_category.label_class AS label, "
                        . "translation_notifications_category.name AS category "
                        . "FROM user_notifications "
                        . "INNER JOIN notifications "
                        . "ON notifications.id = user_notifications.notification_id "
                        . "INNER JOIN notifications_category "
                        . "ON notifications_category.id = notifications.notifications_category_id "
                        . "INNER JOIN translation_notifications_category "
                        . "ON translation_notifications_category.notifications_category_id = notifications_category.id "
                        . "AND translation_notifications_category.translation_language_id = :languageId "
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
                        , $langId, $langId, $langId, $userId, $limit, $offset);
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
        
        public function load_notifications_from_category($userId, $offset, $category, $limit = 5){
            try {
                $this->check_numeric($offset);
                $this->check_numeric($userId);
                $this->check_numeric($limit);
                $this->is_null_or_empty($category);
                
                $langId = TranslationHandler::get_current_language();
                
                $catId = DbHandler::get_instance()->return_query("SELECT id "
                        . "FROM notifications_category "
                        . "WHERE category_name = :name"
                        , $category);
                
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.id AS id, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead, "
                        . "notifications_category.icon_class AS icon, "
                        . "notifications_category.label_class AS label, "
                        . "translation_notifications_category.name AS category "
                        . "FROM user_notifications "
                        . "INNER JOIN notifications "
                        . "ON notifications.id = user_notifications.notification_id "
                        . "AND notifications.notifications_category_id = :catId "
                        . "INNER JOIN notifications_category "
                        . "ON notifications_category.id = notifications.notifications_category_id "
                        . "INNER JOIN translation_notifications_category "
                        . "ON translation_notifications_category.notifications_category_id = notifications_category.id "
                        . "AND translation_notifications_category.translation_language_id = :languageId "
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
                        , reset($catId)["id"], $langId, $langId, $langId, $userId, $limit, $offset);
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
        
        public function create_new_user_notifications($recievers, $prefix){
            try {
                $this->check_recievers($recievers);
                $this->check_prefix($prefix);
                
                foreach ($recievers as $reciever){
                    $this->create_static_user_notification($reciever, $prefix);
                }
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        private function create_static_user_notification($reciever, $prefix){
            DbHandler::get_instance()->Query("INSERT INTO user_notifications (user_id, notification_id, datetime, is_read, sender_user_id) "
                    . "VALUES (:reciever,(SELECT id FROM notifications WHERE prefix=:prefix LIMIT 1),NOW(),:isRead)", $reciever, $prefix, 0);
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
            if (empty($value) && $value != 0) {
                throw new Exception("OBJECT_IS_EMPTY");
            }
        }
        
        
    }
?>

