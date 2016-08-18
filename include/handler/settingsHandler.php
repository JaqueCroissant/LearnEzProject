<?php
class SettingsHandler extends Handler {

    private static $_settings;
    
    private $_current_settings;
    public $language_changed = false;
    
    private static function get_default_settings() {
        $settings_data = DbHandler::get_instance()->return_query("SELECT * FROM user_settings WHERE user_id = :user_id LIMIT 1", 0);
        if(count($settings_data) < 1) {
            throw new Exception();
        }
        
        self::$_settings = new User_Settings(reset($settings_data));
        return self::$_settings;
    }
    
    private static function set_settings() {
        $user = SessionKeyHandler::get_from_session("user", true);
        
        if(!isset($user->settings) || empty($user->settings)) {
            $settings_data = DbHandler::get_instance()->return_query("SELECT * FROM user_settings WHERE user_id = :user_id LIMIT 1", $user->id);
            if(count($settings_data) < 1) {
                return false;
            }
            self::$_settings = new User_Settings(reset($settings_data));
            $user->settings = self::$_settings;
            SessionKeyHandler::add_to_session("user", $user, true);
            return true;
        }
        self::$_settings = $user->settings;
        return true;
    }
    
    private static function fetch_settings_session() {
        if(SessionKeyHandler::session_exists("user"))
        {
            if(!self::set_settings())
            {
                return false;
            }
            return true;
        }
        return false;
    }
    
    public static function get_settings()
    {
        if(!self::fetch_settings_session()) {
            return self::get_default_settings();
        }
        return self::$_settings;
    }
    
    private function assign_language($language_id) {
        
        if($language_id == $this->_current_settings->language_id) {
            return;
        }
        
        $this->language_changed = true;
        $language_ids = DbHandler::get_instance()->return_query("SELECT id FROM language WHERE open = '1'");

        if(count($language_ids) < 1) {
            throw new Exception("INVALID_SETTINGS_INPUT");
        }

        if(!array_value_exists_in_key($language_ids, "id", $language_id)) {
            throw new Exception("INVALID_SETTINGS_INPUT");
        }
        
        TranslationHandler::reset();
    }
    
    private function assign_os($os_id) {
        
        if($os_id == $this->_current_settings->os_id) {
            return;
        }
        
        $os_ids = DbHandler::get_instance()->return_query("SELECT id FROM course_os");

        if(count($os_ids) < 1) {
            throw new Exception("INVALID_SETTINGS_INPUT");
        }

        if(!array_value_exists_in_key($os_ids, "id", $os_id)) {
            throw new Exception("INVALID_SETTINGS_INPUT");
        }
    }
    
    private function assign_blocked_users(&$settings) {
        if(!is_array($settings->blocked_students) || empty($settings->blocked_students)) {
            $settings->blocked_students = null;
            return;
        }
        
        $array = array();
        foreach($settings->blocked_students as $key => $student) {
            if(!is_numeric($student)) {
                unset($settings->blocked_students[$key]);
                continue;
            }
            $array[] = $student;
        }
        
        $count = DbHandler::get_instance()->count_query("SELECT id FROM users WHERE id IN (". generate_in_query($array) .") AND user_type_id = '4'");
        
        if($count != count($array)) {
            throw new Exception("INVALID_SETTINGS_INPUT");
        }
        
        $settings->blocked_students = json_encode($array);
    }
    
    public function initial_update($language_id, $os_id)
    {
        try
        {
            if(!SessionKeyHandler::session_exists("user_setup"))
            {
                echo "SESSIONJUNK";
                throw new Exception("INVALID_SETTINGS_INPUT");
            }

            if(empty($language_id) || empty($os_id) || !is_numeric($language_id) || !is_numeric($os_id))
            {
                throw new Exception("INVALID_SETTINGS_INPUT");
            }

            $this->_current_settings = self::get_settings();
            $user = SessionKeyHandler::get_from_session("user_setup");

            $this->assign_os($os_id);
            $this->assign_language($language_id);

            if(!DbHandler::get_instance()->query("UPDATE user_settings SET language_id = :lang_id, os_id = :os_id WHERE user_id = :user_id", $language_id, $os_id, $user["user_id"]))
            {
                throw new Exception("DATABASE_UNKNOWN_ERROR");
            }

            return true;
        }
        catch(Exception $ex)
        {
            echo $ex->getMessage();
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function update_course_show_order($new_value){
        try {
            if(!RightsHandler::has_page_right("SETTINGS_PREFERENCES")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if ($new_value != 0 && $new_value != 1) {
                throw new Exception("INVALID_INPUT");
            }
            if(DbHandler::get_instance()->count_query("SELECT id FROM user_settings WHERE user_id = :user_id", $this->_user->id) > 0) {
                DbHandler::get_instance()->query("UPDATE user_settings SET course_show_order = :value WHERE user_id = :user", $new_value, $this->_user->id);
                $this->_user->settings->course_show_order = $new_value;
                SessionKeyHandler::add_to_session("user", $this->_user, true);
                return true;
            }
            else {
                throw new Exception("UNKNOWN_ERROR");
            }
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }

    public function update_settings($settings = null) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_page_right("SETTINGS_PREFERENCES")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($settings) || !is_a($settings, 'User_Settings')) {
                throw new Exception("INVALID_SETTINGS_INPUT");
            }
            
            if(empty($settings->user_id) || !is_numeric($settings->user_id) || empty($settings->language_id) || !is_numeric($settings->language_id) || empty($settings->os_id) || !is_numeric($settings->os_id) || empty($settings->elements_shown) || !is_numeric($settings->elements_shown)) {
                throw new Exception("INVALID_SETTINGS_INPUT");
            }
            
            if($settings->user_id != $this->_user->id) {
                throw new Exception("INVALID_SETTINGS_INPUT");
            }
            
            if(!in_array($settings->elements_shown, array(5, 10, 25, 50, 100))) {
                throw new Exception("INVALID_SETTINGS_INPUT");
            }
            
            $this->_current_settings = self::get_settings();
            
            $this->assign_language($settings->language_id);
            $this->assign_os($settings->os_id);
            $this->assign_blocked_users($settings);
            
            if(DbHandler::get_instance()->count_query("SELECT id FROM user_settings WHERE user_id = :user_id", $this->_user->id) > 0) {
                DbHandler::get_instance()->query("UPDATE user_settings SET language_id = :language_id, os_id = :os_id, elements_shown = :elements_shown, block_mail_notifications = :block_mail_notifications, block_student_mails = :block_student_mails, hide_profile = :hide_profile, blocked_students = :blocked_students WHERE user_id = :user_id", $settings->language_id, $settings->os_id, $settings->elements_shown, $settings->block_mail_notifications, $settings->block_student_mails, $settings->hide_profile, $settings->blocked_students, $this->_user->id);
            } else {
                DbHandler::get_instance()->query("INSERT INTO user_settings (user_id, language_id, os_id, elements_shown, block_mail_notifications, block_student_mails, hide_profile, blocked_students, course_show_order) VALUES (:user_id, :language_id, :os_id, :elements_shown, :block_mail_notifications, :block_student_mails, :hide_profile, :blocked_students, 0)", $this->_user->id, $settings->language_id, $settings->os_id, $settings->elements_shown, $settings->block_mail_notifications, $settings->block_student_mails, $settings->hide_profile, $settings->blocked_students);
            }
            
            $this->_user->settings = null;
            SessionKeyHandler::add_to_session("user", $this->_user, true);
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
}