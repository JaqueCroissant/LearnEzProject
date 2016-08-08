<?php
class Homework extends ORM {
    public $user_id;
    public $firstname;
    public $surname;
    
    public $id;
    public $title;
    public $description;
    public $date_assigned;
    public $date_expire;
    public $is_complete = false;
    public $color;
    
    public $class_ids = array();
    public $classes = array();
    
    public $lectures = array();
    public $tests = array();
}
?>