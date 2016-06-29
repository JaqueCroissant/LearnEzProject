<?php
    class RightsHandler extends Handler
    {
        public function __construct() {
            parent::__construct();
            $this->get_from_database();
        }
        
        private function get_from_database()
        {
            if($this->user_exists()) {
                $userRights = DbHandler::get_instance()->return_query("SELECT rights.id, rights.prefix, rights.sort_order, translation_rights.title
                                                        FROM rights INNER JOIN translation_rights ON rights.id = translation_rights.rights_id 
                                                        INNER JOIN user_type_rights ON rights.id = user_type_rights.rights_id
                                                        WHERE user_type_rights.user_type_id = :type AND translation_rights.language_id = :lang", 
                                                        $this->user->user_type_id, translationHandler::get_current_language());

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

        public function right_exists($prefix)
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

        public function reset_rights()
        {
            SessionKeyHandler::remove_from_session("rights");
        }

        public function update_type_rights($user_type, $rights_array)
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