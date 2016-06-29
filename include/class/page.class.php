<?php

class Page extends ORM {
    
    public $id;
    public $master_page_id;
    public $location_id;
    public $sort_order;
    public $page_arguments;
    
    public $is_dropdown;
    public $display_menu;
    public $display_text;
    
    public $title;
    public $pagename;
    public $image;
    
    public $children = array();
}