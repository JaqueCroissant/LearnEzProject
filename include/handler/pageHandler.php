<?php

class pageHandler extends Handler {
    private $_pages = array();
    
    public function __construct() {
        parent::__construct();
        $this->get_pages();
        
        foreach($this->_pages as $key => $value) {
            echo "Key: " . $key . " <br />";
            echo "Value: " . var_dump($value) . "<br /> <br />";
        }
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
            $pageData = DbHandler::getInstance()->ReturnQuery("SELECT page.id, page.master_page_id, page.location_id, page.pagename, page.display_menu, page.sort_order, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id INNER JOIN user_type_page ON user_type_page.page_id = page.id WHERE user_type_page.user_type_id = :user_type_id AND translation_page.language_id = :language_id", $user_type_id, TranslationHandler::getCurrentLanguage());
            
            if(count($pageData) < 1) {
                return;
            }
            
            $pageArray = array();
            foreach($pageData as $key => $value) {
                $pageArray[] = new Page($value);
            }
            
            $this->_pages = $pageArray;
            $this->assign_page_children();
            SessionKeyHandler::AddToSession("pages", $this->_pages, true);
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
    
    private function iterate_page_children(&$element, $function) {
        if(empty($element)) {
            return;
        }
        
        if(is_array($element)) {
            foreach($element as $element_key => $element_value) {
                if(!empty($element_value->children) && count($element_value->children) > 0) {
                    
                    $children = array();
                    foreach($element_value->children as $key => $value) {
                        $children[]= $this->iterate_page_children($value, $function);
                    }
                    $element_value->children = $children;
                    
                } else {
                    if(!$function($element_value)) {
                        unset($element[$element_key]);
                    }
                }
            }
        } else {
            if(!empty($element->children) && count($element->children) > 0) {
                $children = array();
                foreach($element->children as $key => $value) {
                    $children[] = $this->iterate_page_children($value, $function);
                }
                $element->children = $children;

            } else {
                if($function($element)) {
                    return $element;
                }
                return null;
            }
        }
    }
    
    private $current_menu_position = 1;
    private function check_page_position() {
        if(empty(func_get_args()[0]->position_id) || !is_numeric(func_get_args()[0]->position_id)) {
            return false;
        }
        
        return func_get_args()[0]->position_id == $this->current_menu_position;
    }
    
    public function get_menu($position = 1) {
        if(!$this->pages_exists()) {
            return;
        }
        
        $menu_pages = $this->_pages;
        $lol = $this->iterate_page_children($menu_pages, $this->check_page_position());
        
        return $menu_pages;
    }
    
    public function reset_pages() {
        $this->_pages = array();
        $this->generate_pages();
    }
}

