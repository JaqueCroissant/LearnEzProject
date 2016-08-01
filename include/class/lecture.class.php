<?php

class Lecture extends ORM {
    public $id;
    public $user_course_lecture_id;
    public $course_id;
    public $path;
    public $time_length;
    
    public $title;
    public $progress;
    public $is_complete;
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