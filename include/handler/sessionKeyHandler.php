<?php

    class SessionKeyHandler
    {
        public static function AddToSession($key, $value)
        {
            if(is_string($key) && !empty($key))
            {
                $_SESSION[$key] = $value;
                return $_SESSION;
            }

            return null;
        }

        public static function GetFromSession($key)
        {
            if(is_string($key) && self::SessionExists($key))
            {
                return $_SESSION[$key];
            }

            return null;
        }

        public static function RemoveFromSession($key)
        {
            if(is_string($key) && self::SessionExists($key))
            {
                unset($_SESSION[$key]);
                return true;
            }

            return false;
        }

        public static function SessionExists($key)
        {
            if(is_string($key )&& !empty($key))
            {
                return isset($_SESSION[$key]);
            }   

            return false;
        }
    }

?>