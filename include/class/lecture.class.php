<?php

class Lecture extends ORM {
    public $id;
    public $course_lecture_id;
    public $path;
    public $time_length;
    
    public $title;
    public $description;
    public $language_id;
    public $sort_order;
    
    public $points;
    public $advanced;
}

?>