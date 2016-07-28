<?php

class Lecture extends ORM {
    public $id;
    public $course_id;
    public $path;
    public $time_length;
    
    public $title;
    public $course_title;
    public $course_color;
    public $description;
    public $language_id;
    public $sort_order;
    public $image_filename;
    
    public $points;
    public $advanced;
}

?>