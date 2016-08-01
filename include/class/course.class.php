<?php

class Course extends ORM {
    public $id;
    public $os_id;
    
    public $title;
    public $os_title;
    public $description;
    public $language_id;
    public $sort_order;
    public $image_id;
    public $image_filename;
    
    public $points;
    public $color;
    public $overall_progress;
    public $amount_of_lectures = 0;
    public $amount_of_tests = 0;
}

?>