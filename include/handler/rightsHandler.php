<?php
    class RightsHandler
    {
        public static $error;
        
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

        public static function reset_rights()
        {
            SessionKeyHandler::remove_from_session("rights");
        }
        
        public static function update_page_rights($user_type = 1, $page_rights = array()) {
            try {
                if(empty($user_type) || !is_numeric($user_type)) {
                    throw new Exception("INVALID_USER_TYPE");
                }
                
                if(empty($page_rights) || !is_array($page_rights) ) {
                   throw new Exception("INVALID_PAGE_RIGHTS");
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
                echo $ex->getMessage();
                self::$error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }

        public static function update_type_rights($user_type, $rights_array)
        {
            if(is_int($user_type) && $user_type < 5 && $user_type > 0)
            {
                DbHandler::get_instance()->query("DELETE FROM user_type_rights WHERE user_type_id = :id", $user_type);
                $rights = DbHandler::get_instance()->return_query("SELECT * FROM rights");
                
                $new_rights = array();
                foreach($rights as $right)
                {
                    if(in_array($right['prefix'], $rights_array))
                    {
                        array_push($new_rights, $right);
                    }
                }

                $right_count = count($new_rights);
                $insert_values = "";
                
                for($i=0; $i<$right_count; $i++)
                {
                    if($i != 0 && $i!=$right_count)
                    {
                        $insert_values .= ", ";
                    }

                    $right = $new_rights[$i];
                    $insert_values .= "(" . $user_type . ", " . $right['id'] . ")";
                }

                DbHandler::get_instance()->query("INSERT INTO user_type_rights (user_type_id, rights_id) VALUES " . $insert_values);

                return true;
            }
            return false;
        }
    }
?>