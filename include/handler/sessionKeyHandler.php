<?php

    class SessionKeyHandler
    {
        public static function add_to_session($key, $value, $serialize = false)
        {
            if(is_string($key) && !empty($key))
            {
                $_SESSION[$key] = $serialize ? serialize($value) : $value;
                return $_SESSION;
            }
            return null;
        }

        public static function get_from_session($key, $unserialize = false)
        {
            if(is_string($key) && self::session_exists($key))
            {
                return $unserialize ? unserialize($_SESSION[$key]) : $_SESSION[$key];
            }

            return null;
        }

        public static function remove_from_session($key)
        {
            if(is_string($key) && self::session_exists($key))
            {
                unset($_SESSION[$key]);
                return true;
            }

            return false;
        }

        public static function session_exists($key)
        {
            if(!is_string($key) || empty($key))
            {
                throw new Exception("SESSION_INVALID_KEY");
            }

            return isset($_SESSION[$key]);
        }
    }

?>