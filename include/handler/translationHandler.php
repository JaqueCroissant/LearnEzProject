<?php
class TranslationHandler
{
    private $_defaultLanguage = 1;
    
    public $primaryTranslations;
    
    public function __construct(){
        $this->primaryTranslations = $this->getPrimary();
    }
    
    public function getLanguage(){
        $slang = SessionKeyHandler::GetFromSession("user")->languageId;
        if (!empty($slang)) {
            return $slang;
        }
        $clang = $_COOKIE["language_id"];
        if (!empty($clang)) {
            return $clang;
        }
        return $this->_defaultLanguage;
    }
    
    public function setLanguage($language){
        setcookie("language_id", $language);
        if (SessionKeyHandler::SessionExists("user")) {
            $user = SessionKeyHandler::GetFromSession("user");
            $user->languageId = $language;
            SessionKeyHandler::AddToSession("user", $user);
            //TODO Update db
        }
    }
    
    private function getPrimary(){
        $trans = SessionKeyHandler::GetFromSession("primaryTranslations");
        if (!empty($trans)) {
            return $trans;
        }
        //TODO get data from db
        $dbdata = [];
        SessionKeyHandler::AddToSession("primaryTranslations", $dbdata);
        return $dbdata;       
    }
}
?>
