<?php

class TranslationHandler {
    private static $_current_language_id;
    private static $_default_language_id;
    private static $_user_languages = array();
    private static $_static_texts = array();
    
    private static function default_language_exists() {
        if(!empty(self::$_default_language_id)) {
            return true;
        }
        
        if(SessionKeyHandler::session_exists("default_language")) {
            self::$_default_language_id = SessionKeyHandler::get_from_session("default_language");
            return true;
        }
        
        return false;
    }
    
    private static function get_default_language() {
        try
        {
            if(self::default_language_exists()) {
                return self::$_default_language_id;
            }
            
            $user_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            
            if(empty($user_language)) {
                throw new Exception();
            }
            
            self::get_user_languages();
            
            self::$_default_language_id = array_key_exists($user_language, self::$_user_languages) ? self::$_user_languages[$user_language] : self::$_user_languages['en'];
            SessionKeyHandler::add_to_session("default_language", self::$_default_language_id);
            
            return self::$_default_language_id;
	}
	catch (Exception $ex) 
        {
            return self::$_user_languages['en'];
	}
    }
    
    private static function get_user_languages() {
        $language_data = DbHandler::get_instance()->return_query("SELECT * FROM country");
        
        if(is_array(self::$_user_languages) && count(self::$_user_languages) > 0) {
            return;
        }
        
        if(!empty($language_data) && is_array($language_data) && count($language_data) > 0) {
            $array = array();
            foreach($language_data as $key => $value) {
                $array[$value['prefix']] = $value['language_id'];
            } 
            
            self::$_user_languages = $array;
        }
    }
    
    private static function set_current_language($language_id = null) {
        if (SessionKeyHandler::session_exists("user")){
            self::$_current_language_id = SessionKeyHandler::get_from_session("user", true)->settings->language_id;
            SessionKeyHandler::add_to_session("current_language", self::$_current_language_id);
            return;
        }
        
        if(!empty($language_id)) {
            self::$_current_language_id = $language_id;
            SessionKeyHandler::add_to_session("current_language", self::$_current_language_id);
            return;
        }
        
        self::$_current_language_id = self::get_default_language();
        SessionKeyHandler::add_to_session("current_language", self::$_current_language_id);
    }
    
    private static function set_static_texts() {
        $data = DbHandler::get_instance()->return_query("SELECT static_text.prefix, translation_static_text.text FROM translation_static_text INNER JOIN static_text ON static_text.id = translation_static_text.static_text_id WHERE language_id = :language_id", self::get_current_language());
        $array = array();
        foreach ($data as $value) {
            $array[$value["prefix"]] = $value["text"];
        }
        
        self::$_static_texts = $array;
    }
    
    public static function get_static_text($key = null) {
        if(empty(self::$_static_texts) || !is_array(self::$_static_texts) || count(self::$_static_texts) < 1){
            self::set_static_texts();
        }
        
        if(!empty($key) && array_key_exists($key, self::$_static_texts)) {
            return self::$_static_texts[$key];
        }
        return $key;
    }
    
    public static function get_static_texts() {
        if(empty(self::$_static_texts) || !is_array(self::$_static_texts) || count(self::$_static_texts)){
            self::set_static_texts();
        }
        
        return self::$_static_texts;
    }
    
    public static function get_current_language(){
        if (!SessionKeyHandler::session_exists("current_language")) {
            self::set_current_language();
        }
        return SessionKeyHandler::get_from_session("current_language");
    }
    
    public static function update_language($language_id = null){
        if(empty($language_id) || $language_id == self::get_current_language()) {
            return;
        }
        
        $data = DbHandler::get_instance()->count_query("SELECT * FROM translation_language WHERE id = :id" , $language_id);
        
        if(count($data) < 1) {
            return;
        }
        
        if(SessionKeyHandler::session_exists("user")) {
            $user = SessionKeyHandler::get_from_session("user", true);
            $user->language_id = $language_id;
            SessionKeyHandler::add_to_session("user", $user, true);
            DbHandler::get_instance()->query("UPDATE users SET language_id = :language_id WHERE id= :user_id", $language_id, $user->id);
        }
        
        self::set_current_language($language_id);
        self::$_static_texts = array();
    }
    
    public static function reset() {
        SessionKeyHandler::remove_from_session("current_language");
        SessionKeyHandler::remove_from_session("default_language");
        self::$_current_language_id = null;
        self::$_default_language_id = null;
        self::$_user_languages = array();
    }
    
    public static function get_language_options(){
        return DbHandler::get_instance()->return_query("SELECT * FROM translation_language");
    }
}
