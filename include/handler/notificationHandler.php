<?php
    class NotificationHandler extends Handler
    {
        private $_unseen_notifications;
        private $_notifications;
        private $_args;
        
        
        public function __construct() {
            parent::__construct();
            $this->_notifications = array();
        }
        
        //Til senere brug, cleaner op i arguments tabellen
        private function clean_arguments(){
            try {
                DbHandler::get_instance()->query("DELETE FROM user_notifications_arguments "
                        . "WHERE arg_id NOT IN ("
                        . "SELECT arg_id FROM user_notifications)");
                
            } catch (Exception $ex) {

            }
        }
        
        public function get_notification_categories(){
            try {
                $this->check_login();
                $this->check_rights();
                return DbHandler::get_instance()->return_query("SELECT notifications_category.icon_class, "
                        . "notifications_category.category_name, "
                        . "notifications_category.id, "
                        . "translation_notifications_category.name "
                        . "FROM notifications_category "
                        . "INNER JOIN translation_notifications_category "
                        . "ON translation_notifications_category.notifications_category_id = notifications_category.id "
                        . "AND translation_notifications_category.translation_language_id = :langId "
                        . "WHERE notifications_category.master_name = ''", TranslationHandler::get_current_language());
                
            } catch (Exception $exc) {
                $this->error = $exc->getMessage();
            }
        }
        
        public function update_unseen_notification_count(){
            try {
                $this->check_login();
                $this->check_rights();
                $newCounter = DbHandler::get_instance()->count_query("SELECT notification_id "
                        . "FROM user_notifications "
                        . "WHERE user_id=:userId "
                        . "AND is_read=:isRead", $this->_user->id, 0); 
                
                $this->_unseen_notifications = $newCounter;
                
                return true;      
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        public function read_notifications($notifs_array){
            try {
                $this->check_login();
                $this->check_rights();
                $this->check_int_array($notifs_array);
                
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
        
        public function seen_notification($notificationId){
            try {
                $this->check_login();
                $this->check_rights();
                $this->check_numeric($notificationId);
                
                DbHandler::get_instance()->query("UPDATE user_notifications "
                        . "SET is_read=1 "
                        . "WHERE id=:notificationId "
                        . "AND user_id=:userId", $notificationId, $this->_user->id);               
                
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }     
        }
        
        public function seen_notifications(){
            try {
                $this->check_login();
                $this->check_rights();
                
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
                $this->check_login();
                $this->check_rights();
                $this->check_int_array($notifs_array);
                
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
            $this->check_login();
            $this->check_rights();
            return isset($this->_notifications) ? $this->_notifications : array();
        }
        
        public function get_unseen_notifications_count(){
            $this->check_login();
            $this->check_rights();
            return isset($this->_unseen_notifications) ? $this->_unseen_notifications : 0;
        }
        
        public function load_notifications($offset = 0, $limit = 5){
            try {
                $this->check_login();
                $this->check_rights();
                $this->check_numeric($offset);
                $this->check_numeric($limit);
                
                $langId = TranslationHandler::get_current_language();
                
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.id AS id, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead, "
                        . "user_notifications.arg_id AS arg_id, "
                        . "user_notifications.user_id AS user_id, "
                        . "notifications.notifications_category_id AS category_id, "
                        . "notifications_category.icon_class AS icon, "
                        . "notifications_category.link_page AS link_page, "
                        . "notifications_category.link_step AS link_step, "
                        . "notifications_category.link_args AS link_args, "
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
                        . "LIMIT ". $limit . " OFFSET " . $offset
                        , $langId, $langId, $langId, $this->_user->id);
                if (count($dbData) == 0) {
                    $this->_notifications = array();
                    return true;
                }
                $fullArray = array();
                $filtered = $this->filter_blocked_notifications($dbData);
                foreach ($filtered as $notification) {
                    array_push($fullArray, new Notification($notification));
                }
                $this->_notifications = $fullArray;
                $this->seen_notifications();
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        public function load_notifications_from_category($offset = 0, $category = "all", $limit = 5){
            try {
                $this->check_login();
                $this->check_rights();
                $this->check_numeric($offset);
                $this->check_numeric($limit);
                $this->is_null_or_empty($category);
                $this->check_not_numeric($category);
                
                
                $langId = TranslationHandler::get_current_language();
                
                $catIds = DbHandler::get_instance()->return_query("SELECT id "
                        . "FROM notifications_category "
                        . "WHERE category_name = :name "
                        . "OR master_name = :name"
                        , $category, $category);
                
                if (count($catIds) < 1) {
                    throw new Exception("NOTIFICATION_UNKNOWN_CATEGORY");
                }
                
                $categories = "";
                for ($i = 0; $i < count($catIds); $i++){
                    $categories .= ($i == 0 ? "" : ",") . $catIds[$i]["id"];
                }
                
                $dbData = DbHandler::get_instance()->return_query("SELECT translation_notifications.title AS title, "
                        . "translation_notifications.text AS text, "
                        . "user_notifications.id AS id, "
                        . "user_notifications.datetime AS datetime, "
                        . "user_notifications.is_read AS isRead, "
                        . "user_notifications.arg_id AS arg_id, "
                        . "user_notifications.user_id AS user_id, "
                        . "notifications.notifications_category_id AS category_id, "
                        . "notifications_category.icon_class AS icon, "
                        . "notifications_category.link_page AS link_page, "
                        . "notifications_category.link_step AS link_step, "
                        . "notifications_category.link_args AS link_args, "
                        . "translation_notifications_category.name AS category "
                        . "FROM user_notifications "
                        . "INNER JOIN notifications "
                        . "ON notifications.id = user_notifications.notification_id "
                        . "AND notifications.notifications_category_id IN (" . $categories . ") "
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
                        . "LIMIT ". $limit ." OFFSET " . $offset
                        , $langId, $langId, $langId, $this->_user->id);
                if (count($dbData) == 0) {
                    $this->_notifications = array();
                    return true;
                }
                $fullArray = array();
                
                foreach ($dbData as $notification) {
                    array_push($fullArray, new Notification($notification));
                }
                $this->_notifications = $fullArray;
                $this->seen_notifications();
                return true;
                
            } catch (Exception $exc) {
                $this->error = ErrorHandler::return_error($exc->getMessage());
                return false;
            }
        }
        
        public function load_arguments($notifs = array()) {
            try {
                $this->check_login();
                $this->check_rights();
                $this->check_notifs_id($notifs);
                if (count($notifs) > 0) {
                    $ids = array_map(function($e){return $e->arg_id;}, $notifs);
                    $final = "";
                    foreach ($ids as $value) {
                        $final .= "'" . $value . "',";
                    }
                    $final = rtrim($final, ",");
                    $dbData = DbHandler::get_instance()->return_query("SELECT name, value, arg_id "
                            . "FROM user_notifications_arguments "
                            . "WHERE arg_id IN (" . $final . ")");
                    
                    $array = array();
                    foreach ($dbData as $value) {
                        array_push($array, array($value["name"] => $value["value"], "arg_id" => $value["arg_id"]));
                    }
                    $grouped = array_group_by_key($array);
                    $final_array = array();
                    foreach ($grouped as $k => $g) {
                        switch ($k) {
                            case "user": $final_array = array_merge($final_array, $this->change_object_data("user", "users", array("firstname", "surname"), $g)); break;
                            case "class": $final_array = array_merge($final_array, $this->change_object_data("class", "class", array("title"), $g)); break;
                            default: $final_array = array_merge($final_array, $g); break;
                        }
                    }
                    $this->_args = $final_array;
                }
                return array();
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        private function change_object_data($name, $table, $to_get, $filtered_array){
            $objects = array_unique(array_map(function($e) use($name) {return $e[$name];}, $filtered_array));
            $query_ids = "";
            $query_get = "";
            foreach ($objects as $value) {
                $query_ids .= $value . ",";
            }
            foreach ($to_get as $get) {
                $query_get .= $get . ",";
            }
            $query_ids = rtrim($query_ids, ",");
            $query_get = rtrim($query_get, ",");
            $data = DbHandler::get_instance()->return_query("SELECT id, " . $query_get . " FROM " . $table . " WHERE id IN (" . $query_ids . ")");
            $array = array();
            foreach ($filtered_array as $f) {
                foreach ($data as $d) {
                    if ($d["id"] == $f[$name]) {
                        $get_values = "";
                        foreach ($to_get as $g) {
                            $get_values .= ucfirst($d[$g]) . " ";
                        }
                        array_push($array, array($name => $get_values, "arg_id" => $f["arg_id"]));
                        break;
                    }
                }
            }
            return $array;
        }
        
        public function get_arguments($arg_id){
            try {
                $this->check_login();
                $this->check_rights();
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
        
        public static function create_new_static_user_notification($receiver, $prefix, $args){
            try {
                
                if (!isset($receiver) || !is_array($receiver) || (empty($receiver))) {
                    throw new Exception("OBJECT_IS_EMPTY");
                }
                
                if (isset($prefix) && empty($prefix)) {
                    throw new Exception("NOTIFICATION_PREFIX_NOT_SET");
                }

                if (!isset($args) || (empty($args) && $args != 0)) {
                    throw new Exception("OBJECT_IS_EMPTY");
                }

                $guid = "";
                if (count($args) > 0) {
                    $guid = self::create_GUID();
                    $query = "INSERT INTO user_notifications_arguments (name, value, arg_id) VALUES ";
                    foreach ($args as $key => $value) {
                        $query .= "('" . $key . "','" . $value . "','" . $guid . "'),";
                    }
                    $query = rtrim($query, ",") . ";";
                    DbHandler::get_instance()->query($query);
                } 
                
                foreach($receiver as $reciever) {
                    if (is_numeric($reciever) && !is_int((int)($reciever))) {
                        continue;
                    }             

                     DbHandler::get_instance()->Query("INSERT INTO user_notifications (user_id, notification_id, datetime, is_read, arg_id) "
                        . "VALUES (:reciever,(SELECT id FROM notifications WHERE prefix=:prefix LIMIT 1),NOW(),:isRead,:guid)", $reciever, $prefix, 0, $guid);
                }
                
                return true;
                
            } catch (Exception $exc) {
                return false;
            }
        }
        
        
        public static function parse_text($string, $args){
            $array = array_reverse(self::parser($string));
            $final = $string;
            foreach ($array as $value) {
                $sub = substr($string, $value[0] + 2, $value[1]);
                if (isset($args[$sub])) {
                    $final = substr_replace($final, "<b>" . (strlen($args[$sub]) < 50 ? $args[$sub] : (substr($args[$sub], 0, 50) . ".. ")) . "</b>", $value[0], $value[1] + 3);
                    continue;
                }
                $final = substr_replace($final, "<b>" . ucfirst(TranslationHandler::get_static_text("UNKNOWN")) . "</b>", $value[0], $value[1] + 3);
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
        
        private function get_blocked_categories(){
            try{
                //Missing
                return array();
                
                
            } catch (Exception $ex) {

            }
        }
        
        private function filter_blocked_notifications($data){
            $blocked = $this->get_blocked_categories();
            if (count($blocked) > 0) {
                return array_filter($data, function($v) use($blocked) {return (!in_array($v["category_id"], $blocked, true));});
            }
            return $data;
        }
        
        
        private static function create_GUID(){
            if (function_exists('com_create_guid')){
                return com_create_guid();
            }else{
                $charid = md5(uniqid(rand(), true));
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
                throw new Exception("NOTIFICATION_INVALID_ID");
            }
        }
        
        private function check_int_array($ids){
            if (count($ids) < 1) {
                throw new Exception("OBJECT_IS_EMPTY");
            }
            foreach ($ids as $value) {
                $this->check_numeric($value);
            }
        }
        
        private function check_login(){
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
        }
        
        private function is_null_or_empty($value){
            $this->check_isnull($value);
            if (empty($value) && $value != 0) {
                throw new Exception("OBJECT_IS_EMPTY");
            }
        }
        
        private function check_isnull($value){
            if (!isset($value)) {
                throw new Exception("OBJECT_DOESNT_EXIST");
            }
        }
        
        private function check_notifs_id($notifs){
            if (isset($notifs)) {
                foreach ($notifs as $value) {
                    if ($value->user_id != $this->_user->id) {
                        throw new Exception("NOTIFICATION_INVALID_ID");
                    }
                }
            }
        }
        
        private function check_not_numeric($string){
            if (is_numeric($string)) {
                throw new Exception("INVALID_CATEGORY");
            }
        }
        
        private function check_rights(){
            if (!RightsHandler::has_user_right("NOTIFICATIONS")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
        }
    }
?>