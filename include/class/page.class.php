<?php

class Page extends ORM {
    
    public $id;
    public $master_page_id;
    public $location_id;
    public $sort_order;
    public $step;
    
    public $is_dropdown;
    public $display_menu;
    public $display_text;
    
    public $title;
    public $pagename;
    public $icon_class;
    
    public $children = array();
}