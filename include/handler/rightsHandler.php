<?php
    class RightsHandler
    {
        public function GetFromDatabase()
        {
            if(SessionKeyHandler::SessionExists('user'))
            {
                $user = SessionKeyHandler::GetFromSession('user');
                $userRights = DbHandler::getInstance()->ReturnQuery("SELECT prefix, `user_type_rights.user_type_id` AS user_type_id 
                                                        FROM `rights` INNER JOIN `user_type_rights` 
                                                        ON `rights.id` = `user_type_rights.rights_id` 
                                                        WHERE `user_type_id` = :type", $user->userTypeId);
                
                SessionKeyHandler::AddToSession('rights', $userRights);
            }
        }

        public function RightExists($prefix)
        {
            if(is_string($prefix) && !empty($prefix))
            {
                return in_array($prefix, $_SESSION['rights']);
            }

            return false;
        }
    }
?>