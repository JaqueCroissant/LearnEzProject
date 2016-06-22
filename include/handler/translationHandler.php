<?php
class TranslationHandler
{
    private $_defaultLanguage = 1;
    private $_currentLanguage;
    
    public $translation_static_text;
    
    public function __construct(){
    }
    
    public function loadStaticTexts(){
        $this->translation_static_text = $this->getStaticText();
    }
    
    public function getLanguage(){
        if (isset($this->_currentLanguage)) {
            return $this->_currentLanguage;
        }
        $user = SessionKeyHandler::GetFromSession("user");
        if (!empty($user)) {
            return $user->languageId;
        }
        $clang = $_COOKIE["language_id"];
        if (!empty($clang)) {
            return $clang;
        }
        return $this->_defaultLanguage;
    }
    
    public function setLanguage($language){
        setcookie("language_id", $language);
        $this->_currentLanguage = $language;
        if (SessionKeyHandler::SessionExists("user")) {
            $user = SessionKeyHandler::GetFromSession("user");
            $user->languageId = $language;
            SessionKeyHandler::AddToSession("user", $user);
            DbHandler::getInstance()->Query("UPDATE users SET language_id=:languageId WHERE id=:userId", $language, $user->id);
        }
        $this->updateStaticText();
    }
    
    private function getStaticText(){
        $trans = SessionKeyHandler::GetFromSession("static_text");
        if (!empty($trans)) {
            return $trans;
        }
        $this->updateStaticText();
        return SessionKeyHandler::GetFromSession("static_text");
    }
    
    private function updateStaticText(){
        $dbdata = DbHandler::getInstance()->ReturnQuery("SELECT static_text.prefix, translation_static_text.text "
                . "FROM translation_static_text "
                . "INNER JOIN static_text "
                . "ON static_text.id = translation_static_text.static_text_id "
                . "WHERE language_id=:languageId", $this->getLanguage());
        $finalArray = array();
        foreach ($dbdata as $value) {
            $finalArray[$value["prefix"]] = $value["text"];
        }
        SessionKeyHandler::AddToSession("static_text", $finalArray);
    }
}
?>
