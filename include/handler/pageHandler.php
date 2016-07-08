<?php
class pageHandler extends Handler {
    
    public $current_page;
    public $current_page_hierarchy;
    
    private $_pages_raw = array();
    private $_pages = array();
    private $_menu = array();
    private $page_hierarchy_array = array();
   
    public function __construct() {
        parent::__construct();
        $this->get_pages_raw();
        $this->get_pages();
        $this->get_menus();
    }
    
    private function get_pages_raw() {
        if($this->raw_pages_exists()) {
            return;
        }
        
//        if(SessionKeyHandler::session_exists("pages_raw")) {
//            $this->_pages_raw = SessionKeyHandler::get_from_session("pages_raw", true);
//            return;
//        }
        $this->generate_pages();
    }
    
    private function get_pages() {
        if($this->pages_exists()) {
            return;
        }
        
//        if(SessionKeyHandler::session_exists("pages")) {
//            $this->_pages = SessionKeyHandler::get_from_session("pages", true);
//            return;
//        }
        $this->assign_pages();
    }
    
    private function get_menus() {
       if($this->menus_exists()) {
            return;
        }
        
//        if(SessionKeyHandler::session_exists("menu")) {
//            $this->_menu = SessionKeyHandler::get_from_session("menu", true);
//            return;
//        }
        $this->generate_menu();
    }
    
    private function raw_pages_exists() {
        return !empty($this->_pages_raw) && is_array($this->_pages_raw) && count($this->_pages_raw) > 0;
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
            $pageData = DbHandler::get_instance()->return_query("SELECT page.id, page.master_page_id, page.location_id, page.pagename, page.display_menu, page.sort_order, page.page_arguments, page.is_dropdown, page.icon_class, page.display_text, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id INNER JOIN user_type_page ON user_type_page.page_id = page.id WHERE user_type_page.user_type_id = :user_type_id AND translation_page.language_id = :language_id ORDER BY page.sort_order ASC", $user_type_id, TranslationHandler::get_current_language());
            
            if(count($pageData) < 1) {
                return;
            }
            
            foreach($pageData as $page) {
                $key = $page["pagename"] . "" . $page["page_arguments"];
                $this->_pages_raw[$key] = new Page($page);
            }
           
//            SessionKeyHandler::add_to_session("pages_raw", $this->_pages_raw, true);
        }
    }
    
    private function assign_pages() {
        if(empty($this->_pages) || count($this->_pages) < 1) {
            
            if(count($this->_pages_raw) < 1) {
                return;
            }
            
            $pageArray = array();
            foreach($this->_pages_raw as $value) {
                $key = $value->pagename . "" . $value->page_arguments;
                $pageArray[$key] = $value;
            }
            $this->_pages = $pageArray;
            
            $this->assign_page_children();
//            SessionKeyHandler::add_to_session("pages", $this->_pages, true);
        }
    }
    
    private function assign_page_children() {
        if(!$this->pages_exists()) {
            return;
        }
        
        foreach($this->_pages as $key => $value) {
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
                $combined_key = $value->pagename . "" . $value->page_arguments;
                $children[$combined_key] = $value;
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
//            SessionKeyHandler::add_to_session("menu", $this->_menu, true);
        }
    }

    private function get_page_hierarchy($pageArray, $page = null) {

        if($page == null) {
            return reset($pageArray);
        }

        if(is_array($pageArray) && isset($pageArray[$page])) {
            $pageArray[$page]->children = null;
            foreach($pageArray as $key => $value) {
                if($key != $page) {
                    unset($pageArray[$key]);
                }
            }
            return $pageArray;
        }

        if(is_array($pageArray)) {
            foreach($pageArray as $key => $value) {
                $child = $this->get_page_hierarchy($value->children, $page);
                if($child != null) {
                    $pageArray[$key]->children = $child;
                    foreach($pageArray as $outerkey => $outervalue) {
                        if($key != $outerkey) {
                            unset($pageArray[$outerkey]);
                        }
                    }
                return $pageArray;
                }
            }
        }
        return null;
    }
    
    private function iterate_children(&$array, $parent) {
        foreach($parent as $key => $value) {
            $item = clone $value;

            $array[] = $item;
            
            if(is_array($value->children) && count($value->children) > 0) {
                $this->iterate_children($array, $value->children);
            }
        }
    }
    
    private function clone_pages(&$array, $parent) {
        foreach($parent as $key => $value) {
            $item = clone $value;
            
            if(is_array($value->children) && count($value->children) > 0) {
                $this->clone_children($item);
            }
            
            $array[$key] = $item; 
        }
    }
    
    private function clone_children(&$parent) {
        $children = array();
        foreach($parent->children as $key => $value) {
            
            if(is_array($value->children) && count($value->children) > 0) {
                $this->clone_children($value);
            }
            
            $children[$key] = clone $value;
        }
        $parent->children = $children;
    }
    
    public function get_menu($position = 1) {
//        if(!$this->menus_exists()) {
//            return;
//        }
        
        $this->get_menus();
        
//        if(SessionKeyHandler::session_exists("menu")) {
//            $this->_menu = SessionKeyHandler::get_from_session("menu", true);
//        }
        
        if(!array_key_exists($position, $this->_menu)) {
            return;
        }
        $current_menu = $this->_menu[$position];
        
        $array = array();
        $this->iterate_children($array, $current_menu);
        
        return $array;
    }
    
    public function has_rights($pagename) {
        if(!$this->raw_pages_exists()) {
            return false;
        }
        
        if(!array_key_exists($pagename, $this->_pages_raw)) {
            return false;
        }
        
        return true;
    }
    
    public function get_page_from_name($pagename = null, $args = null) {
        try {
            $page_index = $pagename . "" . $args;
            if(empty($pagename) || !preg_match('/^[a-zA-Z_]+$/', $pagename)) {
                throw new Exception ("PAGE_INVALID");
            }

            if(!$this->has_rights($page_index)) {
                throw new Exception ("PAGE_NO_RIGHTS");
            }

            if(!file_exists('../../include/pages/' . $pagename . '.php')) {
                throw new Exception ("PAGE_DOES_NOT_EXIST");
            }
            
            $this->current_page = $this->_pages_raw[$page_index];
            setcookie("current_page", $this->current_page->pagename, time() + (86400 * 30), "/");
            $clone_array = array();
            $this->clone_pages($clone_array, $this->_pages);
            
            $this->current_page_hierarchy = $this->get_page_hierarchy($clone_array, $page_index);
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }
    
    public function get_breadcrumbs_array($page = null) {
        $this->page_hierarchy_array = $page == null ? array() : $this->page_hierarchy_array;
        $page = ($page == null ? reset($this->current_page_hierarchy) : reset($page));
        $this->page_hierarchy_array[] = $page;
        if($page->children != null && is_array($page->children)) {
            $this->get_breadcrumbs_array($page->children);
        }
        return $this->page_hierarchy_array;
    }
    
    
    public function reset() {
        $this->_pages = array();
        $this->_menu = array();
        $this->_pages_raw = array();
        $this->generate_pages();
        $this->assign_pages();
        $this->generate_menu();
    }
    
    public static function page_exists($page = null) {
        if(empty($page) || !preg_match('/^[a-zA-Z_]+$/', $page)) {
            return false;
        }

        if(!file_exists('../../include/pages/' . $page . '.php')) {
            return false;
        }
        
        return DbHandler::get_instance()->count_query("SELECT id FROM page WHERE pagename = :pagename", $page) > 0;
    }
}

