<?php
class TranslationHandler
{
    private static $_defaultLanguage = 1; 
    private $translation_static_text;
    
    public function __construct(){
        if (!SessionKeyHandler::session_exists("current_language")) {
            $this->setCurrentLanguage($this->loadLanguageSettings());
        if (!SessionKeyHandler::SessionExists("current_language")) {
            $this->set_current_language($this->load_language_settings());
        }
        $this->load_static_texts();
    }
    
    public function loadStaticTexts(){
        $trans = SessionKeyHandler::get_from_session("static_text");
    public function load_static_texts(){
        $trans = SessionKeyHandler::GetFromSession("static_text");
        if (!empty($trans)) {
            $this->translation_static_text = $trans;
        }
        else {
<<<<<<< HEAD
            $this->updateStaticText();
            $this->translation_static_text = SessionKeyHandler::get_from_session("static_text");
=======
            $this->update_static_text();
            $this->translation_static_text = SessionKeyHandler::GetFromSession("static_text");
>>>>>>> f5b26dcab1dd77ee1b5287d3cfdf85c8c1f3ae2d
        }
    }
    
    public static function resetLanguage(){
        SessionKeyHandler::remove_from_session("static_text");
        SessionKeyHandler::remove_from_session("current_language");
    }
    
    public static function getCurrentLanguage(){
        if (SessionKeyHandler::session_exists("current_language")) {
            return SessionKeyHandler::get_from_session("current_language");

    public static function reset_language(){
        SessionKeyHandler::RemoveFromSession("static_text");
        SessionKeyHandler::RemoveFromSession("current_language");
    }
    
    public static function get_current_language(){
        if (SessionKeyHandler::SessionExists("current_language")) {
            return SessionKeyHandler::GetFromSession("current_language");
        }
        return self::$_defaultLanguage;
    }
    
    public function get_static_text($key){
        if (array_key_exists($key, $this->translation_static_text)) {
            return $this->translation_static_text[$key];
        }
        return $key;
    }
    
    public function set_language($language){
        if ($language != self::get_current_language()) {
            setcookie("language_id", $language);
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
    
    private static function setCurrentLanguage($language){
        SessionKeyHandler::add_to_session("current_language", $language);
    }
    
    private function loadLanguageSettings(){
        if (SessionKeyHandler::session_exists("user")){
            return SessionKeyHandler::get_from_session("user", true)->language_id;

    private static function set_current_language($language){
        SessionKeyHandler::AddToSession("current_language", $language);
    }
    
    private function load_language_settings(){
        if (SessionKeyHandler::SessionExists("user")){
            return SessionKeyHandler::GetFromSession("user", true)->language_id;

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
