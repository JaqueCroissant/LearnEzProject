<?php
    class NotificationHandler extends Handler
    {
        private $_unseen_notifications;
        private $_unread_notifications;
        private $_notifications;
        private $_args;
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
        
        public function read_notifications($notifs_array){
            try {
                if (!$this->user_exists()) {
                    throw new Exception("USER_NOT_LOGGED_IN");
                }
                if (count($notifs_array) < 1) {
                    throw new Exception("NOTIFICATION_NO_NOTIFICATIONS");
                }
                $values = "";
                
                for ($i = 0; $i < count($notifs_array); $i++){
                    $values .= ($i != 0 ? "," : "") . $notifs_array[$i];
                }
                DbHandler::get_instance()->query("UPDATE user_notifications "
                        . "SET is_read=2 "
                        . "WHERE id IN (" . $values .") "
                        . "AND user_id=:userId", $this->_user->id);
                
                return true;
                
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
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
        
        public function seen_notifications(){
            try {
                if (!$this->user_exists()) {
                    throw new Exception("USER_NOT_LOGGED_IN");;
                }
                
                DbHandler::get_instance()->query("UPDATE user_notifications "
                        . "SET is_read=1 "
                        . "WHERE is_read=0 "
                        . "AND user_id=:userId", $this->_user->id);              
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }          
        }
        
        public function delete_notifications($notifs_array){
            try {
                if (!$this->user_exists()) {
                    throw new Exception("USER_NOT_LOGGED_IN");
                }
                if (count($notifs_array) < 1) {
                    throw new Exception("NOTIFICATION_NO_NOTIFICATIONS");
                }
                $values = "";
                
                for ($i = 0; $i < count($notifs_array); $i++){
                    $values .= ($i != 0 ? "," : "") . $notifs_array[$i];
                }
                DbHandler::get_instance()->query("DELETE FROM user_notifications "
                        . "WHERE id IN (" . $values . ") "
                        . "AND user_id=:userId", $this->_user->id);
                
                return true;
                
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
                return false;
            }
        }
        
        public function get_notifications(){
            return $this->_notifications;
        }
        
        public function get_unseen_notifications_count(){
            return $this->_unseen_notifications;
        }
        
        public function load_notifications($offset, $limit = 5){
            try {
                if (!$this->user_exists()) {
                    throw new Exception("USER_NOT_LOGGED_IN");
                }
                $this->check_numeric($offset);
                $this->check_numeric($limit);
                
                $langId = TranslationHandler::get_current_language();
                
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.id AS id, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead, "
                        . "user_notifications.arg_id AS arg_id, "
                        . "notifications_category.icon_class AS icon, "
                        . "notifications_category.link_page AS page, "
                        . "notifications_category.link_step AS step, "
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
                        , $langId, $langId, $langId, $this->_user->id, $limit, (int)$offset);
                if (count($dbData) == 0) {
                    $this->_notifications = array();
                    return true;
                }
                $ids = array();
                $fullArray = array();
                foreach ($dbData as $notification) {
                    array_push($fullArray, new Notification($notification));
                    array_push($ids, $notification["arg_id"]);
                }
                $this->_args = $this->load_arguments($ids);
                $this->_notifications = $fullArray;
                $this->seen_notifications();
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        public function load_notifications_from_category($offset, $category, $limit = 5){
            try {
                if (!$this->user_exists()) {
                    throw new Exception("USER_NOT_LOGGED_IN");
                }
                $this->check_numeric($offset);
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
                        . "user_notifications.arg_id AS arg_id, "
                        . "notifications_category.icon_class AS icon, "
                        . "notifications_category.link_page AS page, "
                        . "notifications_category.link_step AS step, "
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
                        , reset($catId)["id"], $langId, $langId, $langId, $this->_user->id, $limit, $offset);
                if (count($dbData) == 0) {
                    $this->_notifications = array();
                    return true;
                }
                $ids = array();
                $fullArray = array();
                foreach ($dbData as $notification) {
                    array_push($fullArray, new Notification($notification));
                    array_push($ids, $notification["arg_id"]);
                }
                $this->_args = $this->load_arguments($ids);
                $this->_notifications = $fullArray;
                $this->seen_notifications();
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        private function load_arguments($arg_ids) {
            try {
                $ids = array_unique($arg_ids);
                foreach ($ids as $id) {
                    $this->check_numeric($id);
                }
                $final = "";
                for ($i = 0; $i < count($ids); $i++) {
                   $this->check_numeric($ids[$i]); 
                   $final .= ($i != 0 ? "," : "") . "'" . $ids[$i] . "'";
                }
                $dbData = DbHandler::get_instance()->return_query("SELECT name, value, arg_id "
                        . "FROM user_notifications_arguments "
                        . "WHERE arg_id IN (" . $final . ")");
                $array = array();
                foreach ($dbData as $value) {
                    array_push($array, array($value["name"] => $value["value"], "arg_id" => $value["arg_id"]));
                }
                
                return $array;
                
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        public function get_arguments($arg_id){
            try {
                $this->check_numeric($arg_id);
                
                $new_array = array();
                foreach (array_filter($this->_args, function($v) use($arg_id) {return $v["arg_id"] === $arg_id;}) as $value) {
                    $new_array = array_merge($new_array, $value);
                }
                
                return $new_array;
                
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        public function create_new_static_user_notification($reciever, $prefix, $args){
            try {
                $this->check_numeric($reciever);
                $this->check_prefix($prefix);
                //TODO check $args
                $guid = $this->get_new_guid();
                $query = "INSERT INTO user_notifications_arguments (name, value, arg_id) VALUES ";
                foreach ($args as $key => $value) {
                    $query .= "('" . $key . "','" . $value . "','" . $guid . "'),";
                }
                $query = rtrim($query, ",") . ";";
                echo $query;
                
                DbHandler::get_instance()->query($query);
                
                $this->create_static_user_notification($reciever, $prefix, $guid);
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        
        public static function parse_text($string, $args){
            $array = array_reverse(self::parser($string));
            $final = $string;
            foreach ($array as $value) {
                $sub = substr($string, $value[0] + 2, $value[1]);
                $final = substr_replace($final, "<b>" . (isset($args[$sub]) ? $args[$sub] : "%error%") . "</b>", $value[0], $value[1] + 3);         
            }
            return $final;
        }
        
        private static function parser($string){
            $parse_count = 0;
            $parsing = false;
            $parse_array = array();
            $parse_start = 0;
            $parse_length = 0;
            for($i = 0; $i < strlen($string); $i++) {
                if ($parsing) {
                    if($string[$i] === "%"){
                        $parsing = false;
                        array_push($parse_array, array($parse_start, $parse_length));
                        $parse_length = 0;
                    }
                    else {
                        $parse_length++;
                    }
                }
                else {
                    if ($string[$i] === "%") {
                        $parse_count++;
                        if ($parse_count == 2) {
                            $parsing = true;
                            $parse_start = $i - 1;
                            $parse_count = 0;
                        }
                    }
                    else {
                        $parse_count = 0;
                    }
                }
            }
            return $parse_array;
        }
        
        private function get_new_guid(){
            try{
                while(true){
                    $guid = $this->create_GUID();
                    if (DbHandler::get_instance()->count_query("SELECT id FROM user_notifications_arguments WHERE arg_id = :guid", $guid) < 1) {
                        return $guid;
                    }
                }
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
            }
        }
        
        private function create_GUID(){
            if (function_exists('com_create_guid')){
                return com_create_guid();
            }else{
                $charid = strtoupper(md5(uniqid(rand(), true)));
                $hyphen = chr(45);// "-"
                $uuid = chr(123)// "{"
                    .substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12)
                    .chr(125);// "}"
                return $uuid;
            }
        }
                
        private function create_static_user_notification($reciever, $prefix, $guid){
            DbHandler::get_instance()->Query("INSERT INTO user_notifications (user_id, notification_id, datetime, is_read, arg_id) "
                    . "VALUES (:reciever,(SELECT id FROM notifications WHERE prefix=:prefix LIMIT 1),NOW(),:isRead,:guid)", $reciever, $prefix, 0, $guid);
        }
        
        private function check_prefix($prefix){
            if (isset($prefix) && empty($prefix)) {
                throw new Exception("NOTIFICATION_PREFIX_NOT_SET");
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