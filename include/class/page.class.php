<?php

class Page extends ORM {
    
    public $id;
    public $master_page_id;
    public $location_id;
    public $sort_order;
    
    public $title;
    public $pagename;
    public $display_menu;
    
    public $children = array();
}