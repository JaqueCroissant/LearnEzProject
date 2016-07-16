<?php
    class RightsHandler extends Handler
    {
        public $rights = array();
        public $user_type_rights = array();
        public $category_rights = array();
        
        public function update_page_rights($user_type = 1, $page_rights = array()) {
            try {
                
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
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
                
                if(empty($user_type) || !is_numeric($user_type)) {
                    throw new Exception("INVALID_USER_TYPE");
                }
                
                if(empty($user_rights) || !is_array($user_rights) ) {
                   throw new Exception("INVALID_FORM_DATA");
                }
                
                DbHandler::get_instance()->query("DELETE a.* FROM user_type_rights as a INNER JOIN rights as b ON b.id = a.rights_id WHERE a.user_type_id = :user_type_id AND b.page_right_id = '0'", $user_type);
                
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
        
        public function get_all_rights() {
            try 
            {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }

                $data = DbHandler::get_instance()->return_query("SELECT rights.id, rights.sort_order, rights.page_category_id, translation_rights.title FROM rights INNER JOIN translation_rights ON translation_rights.rights_id = rights.id WHERE page_right_id = '0' AND translation_rights.language_id = :language_id ORDER BY rights.sort_order ASC", TranslationHandler::get_current_language());

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
        
        
        private static function get_from_database()
        {
            if($this->user_exists()) {
                $userRights = DbHandler::get_instance()->return_query("SELECT rights.id, rights.prefix, rights.sort_order, translation_rights.title
                                                        FROM rights INNER JOIN translation_rights ON rights.id = translation_rights.rights_id 
                                                        INNER JOIN user_type_rights ON rights.id = user_type_rights.rights_id
                                                        WHERE user_type_rights.user_type_id = :type AND translation_rights.language_id = :lang", 
                                                        $this->_user->user_type_id, TranslationHandler::get_current_language());

                if(count($userRights)<1)
                {
                    return false;
                }                
                
                $rightArray = array();
                foreach($userRights as $right)
                {
                    $new_right = new Rights($right);
                    $rightArray[$new_right->prefix] = $new_right;
                }
                
                SessionKeyHandler::add_to_session('rights', $rightArray, true);
                return true;
            }
            return false;
        }

        public static function right_exists($prefix)
        {
            if(!SessionKeyHandler::session_exists("rights"))
            {
                if(!$this->get_from_database())
                {
                    return false;
                }
            }

            if(is_string($prefix) && !empty($prefix))
            {
                return array_key_exists($prefix, SessionKeyHandler::get_from_session("rights", true));
            }
            
            return false;
        }

        public static function reset()
        {
            SessionKeyHandler::remove_from_session("rights");
        }

    }
?>