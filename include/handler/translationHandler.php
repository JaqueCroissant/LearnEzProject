<?php
/*
class TranslationHandler extends Handler {
    private $_user_language_prefix;
    private $_user_languages = array();
    
    public function __construct() {
        parent::__construct();
        if (!SessionKeyHandler::session_exists("current_language")) {
            $this->set_current_language($this->load_language_settings());
        }
    }
    
    private function get_browser_language() {
        try
        {
            $user_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            
            if(empty($user_language)) {
                throw new Exception("BROWSER_LANGUAGE_NOT_FOUND");
            }
            
            $this->get_user_languages();
            
            
            
            if(count($data) < 1) {
                throw new Exception("BROWSER_LANGUAGE_NOT_FOUND");
            }
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
    }
    
    private function get_user_languages() {
        $language_data = DbHandler::get_instance()->Query("SELECT * FROM country");
        
        if(is_array($this->_user_languages) && count($this->_user_languages) > 0) {
            return;
        }
        
        if(!empty($language_data) && is_array($language_data) && count($language_data) > 0) {
            
            $array = array();
            foreach($language_data as $key => $value) {
                $array[$value['prefix']] = $value['language_id'];
            } 
            
            $this->_user_languages = $array;
        }
    }
}*/
class TranslationHandler
{
    private static $_defaultLanguage = 1; 
    private $translation_static_text;
    
    public function __construct(){
        if (!SessionKeyHandler::session_exists("current_language")) {
            $this->set_current_language($this->load_language_settings());
        }
        $this->load_static_texts();
    }
    
    public function load_static_texts(){
        $trans = SessionKeyHandler::get_from_session("static_text");
        if (!empty($trans)) {
            $this->translation_static_text = $trans;
        }
        else {
            $this->update_static_text();
            $this->translation_static_text = SessionKeyHandler::get_from_session("static_text");
        }
    }
    public static function reset_language(){
        SessionKeyHandler::remove_from_session("static_text");
        SessionKeyHandler::remove_from_session("current_language");
    }
    
    public static function get_current_language(){
        if (SessionKeyHandler::session_exists("current_language")) {
            return SessionKeyHandler::get_from_session("current_language");
        }
        return self::load_language_settings();
    }
    
    public function get_static_text($key){
        if (array_key_exists($key, $this->translation_static_text)) {
            return $this->translation_static_text[$key];
        }
        return $key;
    }
    
    public function set_language($language){
        if ($language != self::get_current_language()) {
            if (SessionKeyHandler::session_exists("user")) {
                $user = SessionKeyHandler::get_from_session("user", true);
                $user->languageId = $language;
                SessionKeyHandler::add_to_session("user", $user, true);
                DbHandler::get_instance()->query("UPDATE users SET language_id=:languageId WHERE id=:userId", $language, $user->id);
            }
            self::set_current_language($language);
            self::update_static_text();
            $this->load_static_texts();
        }
    }
    
    private static function set_current_language($language){
        SessionKeyHandler::add_to_session("current_language", $language);
    }
    
    private function load_language_settings(){
        if (SessionKeyHandler::session_exists("user")){
            return SessionKeyHandler::get_from_session("user", true)->language_id;

        }
        if (isset($_COOKIE["language_id"])) {
            return $_COOKIE["language_id"];            
        }
        return self::$_defaultLanguage;
    }
    
    private static function update_static_text(){
        $dbdata = DbHandler::get_instance()->return_query("SELECT static_text.prefix, translation_static_text.text "
                . "FROM translation_static_text "
                . "INNER JOIN static_text "
                . "ON static_text.id = translation_static_text.static_text_id "
                . "WHERE language_id=:languageId", self::get_current_language());
        $finalArray = array();
        foreach ($dbdata as $value) {
            $finalArray[$value["prefix"]] = $value["text"];
        }
        SessionKeyHandler::add_to_session("static_text", $finalArray);
    }
}
