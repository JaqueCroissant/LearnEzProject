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


    }
?>