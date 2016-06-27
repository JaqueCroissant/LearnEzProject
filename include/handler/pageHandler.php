<?php

class pageHandler extends Handler {
    private $_pages = array();
    
    public function __construct() {
        parent::__construct();
        $this->get_pages();
    }
    
    private function get_pages() {
        if($this->pages_exists()) {
            return;
        }
        
        if(SessionKeyHandler::SessionExists("pages")) {
            $this->_pages = SessionKeyHandler::GetFromSession("pages", true);
            return;
        }
        $this->generate_pages();
    }
    
    private function pages_exists() {
        return !empty($this->_pages) && is_array($this->_pages) && count($this->_pages) > 0;
    }
    
    private function generate_pages() {
        if(empty($this->_pages) || count($this->_pages) < 1) {
            $user_type_id = $this->user_exists() ? $this->_user->user_type_id : 5;
            $pageData = DbHandler::getInstance()->ReturnQuery("SELECT * FROM page INNER JOIN translation_page ON translation_page.page_id = page.id INNER JOIN user_type_page ON user_type_page.page_id = page.id WHERE user_type_page.user_type_id = :user_type_id AND translation_page.language_id = :language_id", $user_type_id, TranslationHandler::getCurrentLanguage());
            
            $pageArray = array();
            foreach($pageData as $value) {
                $pageArray[] = $value;
            }
            
            $this->_page = $pageArray;
        }
    }
}

