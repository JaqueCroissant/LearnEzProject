<?php
class pageHandler extends Handler {
    
    public $current_page;
    public $current_page_hierarchy;
    public $ordered_pages = array();
    public $page_rights = array();
    
    private $_pages_raw = array();
    private $_pages = array();
    private $_menu = array();
    private $page_hierarchy_array = array();
   
    public function __construct($all_user_types = false) {
        parent::__construct();
        $this->get_pages_raw($all_user_types);
        $this->get_pages();
        $this->get_menus();
    }
    
    private function get_pages_raw($all_user_types = false) {
        if($this->raw_pages_exists()) {
            return;
        }
        
        $this->generate_pages($all_user_types);
    }
    
    private function get_pages() {
        if($this->pages_exists()) {
            return;
        }
        
        $this->assign_pages();
    }
    
    private function get_menus() {
       if($this->menus_exists()) {
            return;
        }
        
        $this->generate_menu();
    }
    
    private function get_ordered_pages() {
       if($this->ordered_pages_exists()) {
            return;
        }
        
        $this->generate_ordered_pages();
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
    
    private function ordered_pages_exists() {
        return !empty($this->ordered_pages) && is_array($this->ordered_pages) && count($this->ordered_pages) > 0;
    }
    
    private function generate_pages($all_user_types = false) {
        if(empty($this->_pages) || count($this->_pages) < 1) {
            $user_type_id = $this->user_exists() ? $this->_user->user_type_id : 5;
            if($all_user_types) {
               $pageData = DbHandler::get_instance()->return_query("SELECT page.id, page.master_page_id, page.location_id, page.pagename, page.display_menu, page.sort_order, page.step, page.is_dropdown, page.icon_class, page.display_text, page.hide_in_backend, page.backend_sort_order, page.backend_category, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id WHERE translation_page.language_id = :language_id ORDER BY page.backend_sort_order ASC, page.sort_order ASC", TranslationHandler::get_current_language());
            } else {
               $pageData = DbHandler::get_instance()->return_query("SELECT page.id, page.master_page_id, page.location_id, page.pagename, page.display_menu, page.sort_order, page.step, page.is_dropdown, page.icon_class, page.display_text, page.hide_in_backend, page.backend_sort_order, page.backend_category, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id INNER JOIN user_type_page ON user_type_page.page_id = page.id WHERE user_type_page.user_type_id = :user_type_id AND translation_page.language_id = :language_id ORDER BY page.sort_order ASC", $user_type_id, TranslationHandler::get_current_language());
             
            }
            
            if(count($pageData) < 1) {
                return;
            }
            
            foreach($pageData as $page) {
                $key = $page["pagename"] . "" . $page["step"];
                $this->_pages_raw[$key] = new Page($page);
            }
        }
    }
    
    private function assign_pages() {
        if(empty($this->_pages) || count($this->_pages) < 1) {
            
            if(count($this->_pages_raw) < 1) {
                return;
            }
            
            $pageArray = array();
            foreach($this->_pages_raw as $value) {
                $key = $value->pagename . "" . $value->step;
                $pageArray[$key] = $value;
            }
            $this->_pages = $pageArray;
            
            $this->assign_page_children();
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
                $combined_key = $value->pagename . "" . $value->step;
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
        }
    }
    
    private function generate_ordered_pages() {
        if(empty($this->ordered_pages) || count($this->ordered_pages) < 1) {
            
            if(!is_array($this->_pages) || empty($this->_pages)) {
                return;
            }

            $ordered_pages = array();
            foreach($this->_pages as $key => $value) {

                if($value->master_page_id > 0) {
                    continue;
                }

                array_push($ordered_pages, clone $value);
            }

            $this->ordered_pages = $ordered_pages;
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
    
    private function iterate_children_remove_children(&$array, $parent) {
        $total_children = 0;
        foreach($parent as $key => $value) {
            $item = clone $value;

            if($value->hide_in_backend) {
                continue;
            }
            
            $array[$value->pagename . '' . $value->step] = $item;
            
            $child_count = 0;
            foreach($value->children as $child) {
               if(!$child->hide_in_backend) {
                    $child_count++;
               } 
            }

            if(is_array($value->children) && count($value->children) > 0) {
                $total_children = $child_count + $this->iterate_children_remove_children($array, $value->children);
            }
            $array[$value->pagename . '' . $value->step]->total_children = $array[$value->pagename . '' . $value->step]->total_children + $total_children;
        }
        return $total_children;
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
        $this->get_menus();
        
        if(!array_key_exists($position, $this->_menu)) {
            return;
        }
        $current_menu = $this->_menu[$position];
        
        $array = array();
        $this->iterate_children($array, $current_menu);
        
        return $array;
    }
    
    public function fetch_ordered_pages() {
        $this->get_ordered_pages();
        
        $current = $this->ordered_pages;
        
        $array = array();
        $this->iterate_children_remove_children($array, $current);
        
       
        $new_array = array();
        foreach($array as $key => $value) {
            if(!(count($value->children) > 0)) {
                $new_array[$key] = $value;
            }

            foreach($value->children as $c_key => $c_value) {
                if(!array_key_exists($c_key, $array)) {
                    unset($value->children[$c_key]);
                }
            }
            $new_array[$key] = $value;
        }
        return $new_array;
    }
    
    public function fetch_rights_page_categories() {
        $this->get_pages_raw();
        
        $array = array();
        foreach($this->_pages_raw as $key => $value) {
            if($value->backend_category) {
                $array[$key] = clone $value;
            }
        }
        
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
    
    public function get_page_from_name($pagename = null, $step = null, $args = null) {
        try {
            $page_index = $pagename . "" . $step;
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
            setcookie("current_page_step", $step, time() + (86400 * 30), "/");
            setcookie("current_page_args", $args, time() + (86400 * 30), "/");
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
    
    public function generate_breadcrumbs($array = array()) {
        $breadcrumbs = "";
        for($i = 0; $i < count($array); $i++) {
            if($array[$i]->is_dropdown || $i+1 >= count($array)) {
                $breadcrumbs .= '<span class="">' . $array[$i]->title . '</span>';    
            } else {
                $breadcrumbs .= '<a class="change_page text-white fw-600" ';
                $breadcrumbs .= ' page="'. $array[$i]->pagename .'" step="'. $array[$i]->step . '"';
                $breadcrumbs .= ' id="'.$array[$i]->pagename.'" href="#">'. $array[$i]->title .' </a>'; 
            }
            if ($i < count($array)-1) {
                $breadcrumbs .= '<span class="material_font">
                    <span class="zmdi-chevron-right p-v-xs"></span>
                </span>';
            } 
        }
        return $breadcrumbs;
    }
    
    public function reset() {
        $this->_pages = array();
        $this->_menu = array();
        $this->_pages_raw = array();
        $this->generate_pages();
        $this->assign_pages();
        $this->generate_menu();
    }
    
    public function generate_page($pagename = null, $step = null) {
        $this->get_pages_raw();
        
        if(array_key_exists($pagename ."". $step, $this->_pages_raw)) {
            return array($this->_pages_raw[$pagename ."" . $step]);
        }
        return array($this->_pages_raw["error"]);
    }
    
    public function get_page_rights($user_type_id = 1) {
        try 
        {
            if(!is_numeric($user_type_id)) {
                throw new Exception("INVALID_USER_TYPE");
            }
            
            if($user_type_id > 5 || $user_type_id < 1) {
                throw new Exception("INVALID_USER_TYPE");
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT user_type_page.id, user_type_page.user_type_id, user_type_page.page_id FROM user_type_page INNER JOIN page ON page.id = user_type_page.page_id WHERE user_type_id = :user_type_id AND page.hide_in_backend != 1", $user_type_id);
            
            if(count($data) < 1) {
                return true;
            }
            
            $array = array();
            
            foreach($data as $page) {
                $array[$page["page_id"]] = $page["page_id"];
            }
            
            $this->page_rights = $array;
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
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

