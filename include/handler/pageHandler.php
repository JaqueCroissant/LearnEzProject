<?php

class pageHandler extends Handler {
    private $_pages_raw = array();
    private $_pages = array();
    private $_menu = array();
   
    public function __construct() {
        parent::__construct();
        $this->get_pages();
        $this->get_menus();
        
        /*foreach($this->_pages as $key => $value) {
            echo "Key: " . $key . " <br />";
            echo "Value: " . var_dump($value) . "<br /> <br />";
        }*/
    }
    
    private function get_pages() {
        if($this->pages_exists()) {
            return;
        }
        
        if(SessionKeyHandler::session_exists("pages")) {
            $this->_pages = SessionKeyHandler::get_from_session("pages", true);
            return;
        }
        $this->generate_pages();
    }
    
    private function get_menus() {
       if($this->menus_exists()) {
            return;
        }
        
        if(SessionKeyHandler::session_exists("menu")) {
            $this->_menu = SessionKeyHandler::get_from_session("menu", true);
            return;
        }
        $this->generate_menu();
    }
    
    private function pages_exists() {
        return !empty($this->_pages) && is_array($this->_pages) && count($this->_pages) > 0;
    }
    
    private function menus_exists() {
        return !empty($this->_menu) && is_array($this->_menu) && count($this->_menu) > 0;
    }
    
    private function generate_pages() {
        if(empty($this->_pages) || count($this->_pages) < 1) {
            $user_type_id = $this->user_exists() ? $this->_user->user_type_id : 5;
            $pageData = DbHandler::get_instance()->return_query("SELECT page.id, page.master_page_id, page.location_id, page.pagename, page.display_menu, page.sort_order, page.page_arguments, page.is_dropdown, page.image, page.display_text, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id INNER JOIN user_type_page ON user_type_page.page_id = page.id WHERE user_type_page.user_type_id = :user_type_id AND translation_page.language_id = :language_id ORDER BY page.sort_order ASC", $user_type_id, TranslationHandler::get_current_language());
            
            if(count($pageData) < 1) {
                return;
            }
            
            $pageArray = array();
            foreach($pageData as $key => $value) {
                $pageArray[] = new Page($value);
            }
            
            $this->_pages = $pageArray;
            $this->assign_page_children();
            SessionKeyHandler::add_to_session("pages", $this->_pages, true);
        }
    }
    
    private function assign_page_children() {
        if(!$this->pages_exists()) {
            return;
        }
        
        foreach($this->_pages as $value) {
            if(empty($value->id) || !is_numeric($value->id)) {
                continue;
            }
            
            $children = $this->find_children($value->id);
            $value->children = $children;       
        }
    }
    
    private function find_children($id) {
        if(!$this->pages_exists()) {
            return;
        }
        
        $children = array();
        $keys = array();
        foreach($this->_pages as $key => $value) {
            if(empty($value->master_page_id) || !is_numeric($value->master_page_id)) {
                continue;
            }
            
            if($value->master_page_id == $id) {
                $children[] = $value;
                $keys[] = $key;
            }
        }
        
        foreach($keys as $key) {
            if(array_key_exists($key, $this->_pages)) {
                unset($this->_pages[$key]);
            }
        }
        return $children;
    }
    
    private function generate_menu() {
        if(empty($this->_menu) || count($this->_menu) < 1) {
            $new_menu = array();
            for($i = 1; $i < 3; $i++) {
                if(!is_array($this->_pages) || empty($this->_pages)) {
                    return;
                }

                $menu = array();
                foreach($this->_pages as $key => $value) {

                    if($value->master_page_id > 0) {
                        continue;
                    }

                    if($value->location_id == $i) {
                        array_push($menu, clone $value);
                    }
                }
                $new_menu[$i] = $menu;
            }
            $this->_menu = $new_menu;
            SessionKeyHandler::add_to_session("menu", $this->_menu, true);
        }
    }
    
    public function get_menu($position = 1) {
        if(!$this->menus_exists()) {
            return;
        }
        
        if(SessionKeyHandler::session_exists("menu")) {
            $this->_menu = SessionKeyHandler::get_from_session("menu", true);
        }
        
        if(array_key_exists($position, $this->_menu)) {
            return $this->_menu[$position];
        }
    }
    
    public function reset() {
        $this->_pages = array();
        $this->_menu = array();
        $this->generate_pages();
        $this->generate_menu();
    }
}

