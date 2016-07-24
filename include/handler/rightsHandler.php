<?php
    class RightsHandler extends Handler
    {
        public $rights = array();
        public $user_type_rights = array();
        public $school_rights = array();
        public $category_rights = array();
        
        public function create_school_rights($school_id) {
            try {
                
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }
                
                if (!RightsHandler::has_user_right("SCHOOL_CREATE")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(empty($school_id) || !is_numeric($school_id)) {
                    throw new Exception("USER_INVALID_SCHOOL_ID");
                }
                
                if(!(DbHandler::get_instance()->count_query("SELECT id FROM school WHERE id = :id", $school_id) > 0)) {
                    throw new Exception("USER_INVALID_SCHOOL_ID"); 
                }
                
                for($i = 2; $i < 5; $i++) {
                    
                    $right_data = DbHandler::get_instance()->return_query("SELECT rights.id, user_type_rights.user_type_id FROM rights INNER JOIN user_type_rights ON user_type_rights.rights_id = rights.id WHERE user_type_rights.user_type_id = :user_type_id AND rights.is_school_right = '1'", $i);
                    
                    if(count($right_data) > 0) {
                        foreach($right_data as $value) {
                            DbHandler::get_instance()->query("INSERT INTO school_rights (school_id, user_type_id, rights_id) VALUES (:school_id, :user_type_id, :rights_id)", $school_id, $value["user_type_id"], $value["id"]);
                        }
                    }
                }
                
            } catch (Exception $ex) {
                echo $ex->getMessage();
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        public function update_page_rights($user_type = 1, $page_rights = array()) {
            try {
                
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }
                
                if (!RightsHandler::has_user_right("RIGHTS")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(empty($user_type) || !is_numeric($user_type)) {
                    throw new Exception("INVALID_USER_TYPE");
                }
                
                if(empty($page_rights) || !is_array($page_rights) ) {
                   throw new Exception("INVALID_FORM_DATA");
                }
                
                DbHandler::get_instance()->query("DELETE a.* FROM user_type_page as a INNER JOIN page as b ON b.id = a.page_id WHERE a.user_type_id = :user_type_id AND b.hide_in_backend != 1", $user_type);
                
                foreach($page_rights as $key => $value) {
                    if(empty($value) || !is_numeric($value)) {
                        continue;
                    }
                    DbHandler::get_instance()->query("INSERT INTO user_type_page (page_id, user_type_id) VALUES (:page_id, :user_type_id)", $value, $user_type);
                }
                
                return true;
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        public function update_rights($user_type = 1, $user_rights = array()) {
            try {
                
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }
                
                if (!RightsHandler::has_user_right("RIGHTS")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(empty($user_type) || !is_numeric($user_type)) {
                    throw new Exception("INVALID_USER_TYPE");
                }
                
                DbHandler::get_instance()->query("DELETE a.* FROM user_type_rights as a INNER JOIN rights as b ON b.id = a.rights_id WHERE a.user_type_id = :user_type_id AND b.page_right_id = '0'", $user_type);
                
                if(empty($user_rights) || !is_array($user_rights) ) {
                   return true;
                }
                
                foreach($user_rights as $key => $value) {
                    if(empty($value) || !is_numeric($value)) {
                        continue;
                    }
                    DbHandler::get_instance()->query("INSERT INTO user_type_rights (rights_id, user_type_id) VALUES (:rights_id, :user_type_id)", $value, $user_type);
                }
                
                return true;
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        public function update_school_rights($user_type_id = 2, $school_rights = array()) {
            try {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }
                if (!RightsHandler::has_user_right("SCHOOL_RIGHTS")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(empty($user_type_id) || !is_numeric($user_type_id)) {
                    throw new Exception("INVALID_USER_TYPE");
                }
                
                if($user_type_id > 5 || $user_type_id < 2) {
                    throw new Exception("INVALID_USER_TYPE");
                }
                
                DbHandler::get_instance()->query("DELETE FROM school_rights WHERE school_id = :school_id AND user_type_id = :user_type_id", $this->_user->school_id, $user_type_id);
                
                if(empty($school_rights) || !is_array($school_rights) ) {
                   return true;
                }
                
                foreach($school_rights as $key => $value) {
                    if(empty($value) || !is_numeric($value)) {
                        continue;
                    }
                    DbHandler::get_instance()->query("INSERT INTO school_rights (school_id, rights_id, user_type_id) VALUES (:school_id, :rights_id, :user_type_id)", $this->_user->school_id, $value, $user_type_id);
                }
                
                return true;
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        public function get_all_rights($school_rights = false) {
            try 
            {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }

                $data = DbHandler::get_instance()->return_query("SELECT rights.id, rights.sort_order, rights.page_category_id, translation_rights.title FROM rights INNER JOIN translation_rights ON translation_rights.rights_id = rights.id WHERE page_right_id = '0' ". ($school_rights ? "AND is_school_right = '1' " : "") ." AND translation_rights.language_id = :language_id ORDER BY rights.sort_order ASC", TranslationHandler::get_current_language());

                if(count($data) < 1) {
                    return true;
                }

                $array = array();
                $category_array = array();

                foreach($data as $rights) {
                    $right = new Rights($rights);
                    $array[$right->id] = $right;
                    $category_array[$right->page_category_id][] = $right;
                }
                $this->rights = $array;
                $this->category_rights = $category_array;

                return true;
            }
            catch (Exception $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        public function get_user_type_rights($user_type_id = 1) {
            try 
            {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }
                
                if(!is_numeric($user_type_id)) {
                    throw new Exception("INVALID_USER_TYPE");
                }

                if($user_type_id > 5 || $user_type_id < 1) {
                    throw new Exception("INVALID_USER_TYPE");
                }

                $data = DbHandler::get_instance()->return_query("SELECT user_type_rights.id, user_type_rights.user_type_id, user_type_rights.rights_id FROM user_type_rights WHERE user_type_id = :user_type_id", $user_type_id);

                if(count($data) < 1) {
                    return true;
                }

                $array = array();

                foreach($data as $right) {
                    $array[$right["rights_id"]] = $right["rights_id"];
                }

                $this->user_type_rights = $array;
                return true;
            }
            catch (Exception $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        public function get_school_rights($user_type_id = 1) {
            try 
            {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }
                
                if(!RightsHandler::has_user_right("SCHOOL_RIGHTS")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(!is_numeric($user_type_id)) {
                    throw new Exception("INVALID_USER_TYPE");
                }

                if($user_type_id > 5 || $user_type_id < 1) {
                    throw new Exception("INVALID_USER_TYPE");
                }

                $data = DbHandler::get_instance()->return_query("SELECT school_rights.id, school_rights.rights_id, school_rights.school_id, school_rights.user_type_id FROM school_rights WHERE user_type_id = :user_type_id AND school_id = :school_id", $user_type_id, $this->_user->school_id);

                if(count($data) < 1) {
                    return true;
                }

                $array = array();

                foreach($data as $right) {
                    $array[$right["rights_id"]] = $right["user_type_id"];
                }

                $this->school_rights = $array;
                return true;
            }
            catch (Exception $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        
        public static function set_rights()
        {
            try 
            {
                $current_user = null;
                if (SessionKeyHandler::session_exists("user")) {
                    $current_user = SessionKeyHandler::get_from_session("user", true);
                }
                
                $page_rights = DbHandler::get_instance()->return_query("SELECT page.pagename, page.step FROM page
                                                        INNER JOIN user_type_page ON user_type_page.page_id = page.id
                                                        WHERE user_type_page.user_type_id = :user_type_id", ($current_user != null ? $current_user->user_type_id : 5));
                
                if(count($page_rights) < 1) {
                    throw new Exception();
                }   
                
                $array = array();
                foreach($page_rights as $right) {
                    $prefix = (!empty($right["pagename"]) ? strtoupper($right["pagename"]) : "") ."". (!empty($right["step"]) ? "_".strtoupper($right["step"]) : "");
                    $array["PAGE_".$prefix] = true;
                }
                
                if($current_user == null) {
                    SessionKeyHandler::add_to_session('rights', $array, true);
                    return true;
                }
                
                $final_user_rights = array();
                $user_rights = DbHandler::get_instance()->return_query("SELECT rights.id, rights.prefix, rights.sort_order, rights.is_school_right  FROM rights
                                                        LEFT JOIN user_type_rights ON rights.id = user_type_rights.rights_id
                                                        LEFT JOIN page ON page.id = rights.page_right_id
                                                        LEFT JOIN user_type_page ON user_type_page.page_id = rights.page_right_id
                                                        WHERE user_type_rights.user_type_id = :user_type_id OR user_type_page.user_type_id = :user_type_id", 
                                                        $current_user->user_type_id, $current_user->user_type_id);

                if($current_user != null && $current_user->user_type_id != 1) {
                    $data = DbHandler::get_instance()->return_query("SELECT school_rights.rights_id, rights.id, rights.prefix FROM school_rights RIGHT JOIN rights ON rights.id = school_rights.rights_id WHERE school_rights.user_type_id = :user_type_id AND school_id = :school_id", $current_user->user_type_id, $current_user->school_id);
                    
                    foreach($user_rights as $key => $value) {
                        if(isset($value["is_school_right"]) && !empty($value["is_school_right"]) && $value["is_school_right"]) {
                            unset($user_rights[$key]);
                        }
                    }
                    
                    if(count($data) > 0) {
                        $user_rights = array_merge($user_rights, $data);
                    }
                }
                
                if(count($user_rights) < 1) {
                    throw new Exception();
                }                

                foreach($user_rights as $right) {
                    $array["RIGHT_".strtoupper($right["prefix"])] = true;
                }
                
                SessionKeyHandler::add_to_session('rights', $array, true);
                return true;

            }
            catch (Exception $ex) 
            {
                echo $ex->getMessage();
            }
            return false;
        }
        
        private static function fetch_rights_session() {
            if(!SessionKeyHandler::session_exists("rights"))
            {
                if(!self::set_rights())
                {
                    return false;
                }
                return true;
            }
            return true;
        }

        public static function has_user_right($prefix)
        {
            if(!self::fetch_rights_session()) {
                return false;
            }
            if(is_string($prefix) && !empty($prefix))
            {
                return array_key_exists("RIGHT_".strtoupper($prefix), SessionKeyHandler::get_from_session("rights", true));
            }
            return false;
        }
        
        public static function has_page_right($prefix)
        {
            if(!self::fetch_rights_session()) {
                return false;
            }

            if(is_string($prefix) && !empty($prefix))
            {
                return array_key_exists("PAGE_".strtoupper($prefix), SessionKeyHandler::get_from_session("rights", true));
            }
            return false;
        }
        
        private static $_rights_list = array();
        private static $_current_right;
        private static $_current_user;
        
        private static function assign_temporary_rights() {
            $final = array();
            $rights = DbHandler::get_instance()->return_query("SELECT id, is_school_right, prefix FROM rights");
            
            if(count($rights) < 1) {
                self::$_rights_list = array();
                return;
            }
            
            foreach($rights as $right) {
                $final[$right["prefix"]] = new Rights($right);
            }
            self::$_rights_list = $final;
        }
        
        public static function target_has_right($user_id, $prefix) {
            try 
            {
                if(!is_numeric($user_id) || !is_string($prefix) || empty($prefix) || empty($user_id)) {
                    return false;
                }
                
                if(empty(self::$_rights_list)) {
                    self::assign_temporary_rights();
                }
                
                if(!array_key_exists($prefix, self::$_rights_list)) {
                    return false;
                }
                
                self::$_current_right = self::$_rights_list[$prefix];
                
                $current_user = DbHandler::get_instance()->return_query("SELECT id, school_id, user_type_id FROM users WHERE id = :user_id LIMIT 1", $user_id);
                
                if(empty($current_user)) {
                    return false;
                }
                
                self::$_current_user = new User(reset($current_user));
                
                if(self::$_current_user->user_type_id == 1 || !self::$_current_right->is_school_right) {
                    $right = DbHandler::get_instance()->count_query("SELECT id FROM user_type_rights WHERE user_type_id = :user_type_id AND rights_id = :right_id", self::$_current_user->user_type_id, self::$_current_right->id);
                    return $right > 0;     
                }
                
                $right = DbHandler::get_instance()->count_query("SELECT id FROM school_rights WHERE user_type_id = :user_type_id AND rights_id = :right_id AND school_id = :school_id", self::$_current_user->user_type_id, self::$_current_right->id, self::$_current_user->school_id);
                return $right > 0;
            }
            catch (Exception $ex) 
            {
                return false;
            }
        }

        public static function reset()
        {
            SessionKeyHandler::remove_from_session("rights");
        }
    }
?>