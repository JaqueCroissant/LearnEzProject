<?php
    class RightsHandler
    {
        public function GetFromDatabase()
        {
            if(SessionKeyHandler::SessionExists('user'))
            {
                $user = SessionKeyHandler::GetFromSession('user');
                $userRights = DbHandler::getInstance()->ReturnQuery("SELECT rights.prefix
                                                        FROM rights INNER JOIN user_type_rights 
                                                        ON rights.id = user_type_rights.rights_id 
                                                        WHERE user_type_rights.user_type_id = :type", $user->user_type_id);
                
                $rightArray = array();
                foreach(reset($userRights) as $right)
                {
                    array_push($rightArray, $right);
                }

                SessionKeyHandler::AddToSession('rights', $rightArray);
            }
        }

        public function RightExists($prefix)
        {
            if(is_string($prefix) && !empty($prefix))
            {
                return in_array($prefix, SessionKeyHandler::GetFromSession("rights"));
            }

            return false;
        }

        public function UpdateTypeRights($user_type, $rights_array)
        {
            if(is_int($user_type) && $user_type < 5 && $user_type > 0)
            {
                DbHandler::getInstance()->Query("DELETE FROM user_type_rights WHERE user_type_id = :id", $user_type);
                $rights = DbHandler::getInstance()->ReturnQuery("SELECT * FROM rights");
                
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

                DbHandler::getInstance()->Query("INSERT INTO user_type_rights (user_type_id, rights_id) VALUES " . $insert_values);

                return true;
            }

            return false;
        }
    }
?>