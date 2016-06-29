<?php
class TranslationHandler
{
    private static $_defaultLanguage = 1; 
    private $translation_static_text;
    
    public function __construct(){
        if (!SessionKeyHandler::SessionExists("current_language")) {
            $this->setCurrentLanguage($this->loadLanguageSettings());
        }
        $this->loadStaticTexts();
    }
    
    public function loadStaticTexts(){
        $trans = SessionKeyHandler::GetFromSession("static_text");
        if (!empty($trans)) {
            $this->translation_static_text = $trans;
        }
        else {
            $this->updateStaticText();
            $this->translation_static_text = SessionKeyHandler::GetFromSession("static_text");
        }
    }
    
    public static function resetLanguage(){
        SessionKeyHandler::RemoveFromSession("static_text");
        SessionKeyHandler::RemoveFromSession("current_language");
    }
    
    public static function getCurrentLanguage(){
        if (SessionKeyHandler::SessionExists("current_language")) {
            return SessionKeyHandler::GetFromSession("current_language");
        }
        return self::$_defaultLanguage;
    }
    
    public function getStaticText($key){
        if (array_key_exists($key, $this->translation_static_text)) {
            return $this->translation_static_text[$key];
        }
        return $key;
    }
    
    public function setLanguage($language){
        if ($language != self::getCurrentLanguage()) {
            setcookie("language_id", $language);
            if (SessionKeyHandler::SessionExists("user")) {
                $user = SessionKeyHandler::GetFromSession("user", true);
                $user->languageId = $language;
                SessionKeyHandler::AddToSession("user", $user, true);
                DbHandler::get_instance()->query("UPDATE users SET language_id=:languageId WHERE id=:userId", $language, $user->id);
            }
            self::setCurrentLanguage($language);
            self::updateStaticText();
            $this->loadStaticTexts();
        }
    }
    
    private static function setCurrentLanguage($language){
        SessionKeyHandler::AddToSession("current_language", $language);
    }
    
    private function loadLanguageSettings(){
        if (SessionKeyHandler::SessionExists("user")){
            return SessionKeyHandler::GetFromSession("user", true)->language_id;
        }
        if (isset($_COOKIE["language_id"])) {
            return $_COOKIE["language_id"];            
        }
        return self::$_defaultLanguage;
    }
    
    private static function updateStaticText(){
        $dbdata = DbHandler::get_instance()->return_query("SELECT static_text.prefix, translation_static_text.text "
                . "FROM translation_static_text "
                . "INNER JOIN static_text "
                . "ON static_text.id = translation_static_text.static_text_id "
                . "WHERE language_id=:languageId", self::getCurrentLanguage());
        $finalArray = array();
        foreach ($dbdata as $value) {
            $finalArray[$value["prefix"]] = $value["text"];
        }
        SessionKeyHandler::AddToSession("static_text", $finalArray);
    }
}
