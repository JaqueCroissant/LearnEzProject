<?php

class Lecture extends ORM {
    public $id;
    public $course_id;
    public $path;
    public $time_length;
    
    public $title;
    public $description;
    public $language_id;
    
    public $points;
    public $advanced;
}

?>