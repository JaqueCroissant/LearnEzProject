<?php

    class SessionKeyHandler
    {
        public static function AddToSession($key, $value, $serialize = false)
        {
            if(is_string($key) && !empty($key))
            {
                $_SESSION[$key] = $serialize ? serialize($value) : $value;
                return $_SESSION;
            }
            return null;
        }

        public static function GetFromSession($key, $unserialize = false)
        {
            if(is_string($key) && self::SessionExists($key))
            {
                return $unserialize ? unserialize($_SESSION[$key]) : $_SESSION[$key];
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