<?php
class TranslationHandler
{
    private $_defaultLanguage = 1; 
    private$translation_static_text;
    
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
    
    public static function getCurrentLanguage(){
        return SessionKeyHandler::GetFromSession("current_language");
    }
    
    public function getStaticText($key){
        return $this->translation_static_text[$key];
    }
    
    public function setLanguage($language){
        if ($language != TranslationHandler::getCurrentLanguage()) {
            setcookie("language_id", $language);
            if (SessionKeyHandler::SessionExists("user")) {
                $user = SessionKeyHandler::GetFromSession("user");
                $user->languageId = $language;
                SessionKeyHandler::AddToSession("user", $user);
                DbHandler::getInstance()->Query("UPDATE users SET language_id=:languageId WHERE id=:userId", $language, $user->id);
            }
            TranslationHandler::setCurrentLanguage($language);
            TranslationHandler::updateStaticText();
            $this->loadStaticTexts();
        }
    }
    
    private static function setCurrentLanguage($language){
        SessionKeyHandler::AddToSession("current_language", $language);
    }
    
    private function loadLanguageSettings(){
        if (SessionKeyHandler::SessionExists("user")){
            return SessionKeyHandler::GetFromSession("user")->languageId;
        }
        if (isset($_COOKIE["language_id"])) {
            return $_COOKIE["language_id"];            
        }
        return $this->_defaultLanguage;
    }
    
    private static function updateStaticText(){
        $dbdata = DbHandler::getInstance()->ReturnQuery("SELECT static_text.prefix, translation_static_text.text "
                . "FROM translation_static_text "
                . "INNER JOIN static_text "
                . "ON static_text.id = translation_static_text.static_text_id "
                . "WHERE language_id=:languageId", TranslationHandler::getCurrentLanguage());
        $finalArray = array();
        foreach ($dbdata as $value) {
            $finalArray[$value["prefix"]] = $value["text"];
        }
        SessionKeyHandler::AddToSession("static_text", $finalArray);
    }
}
?>
