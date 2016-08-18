<?php

class Certificate extends ORM {
    
    public $id;
    public $is_completed;
    public $completion_date;
    public $validation_code;
    
    public $course_id;
    public $course_title;
    public $course_description;
    public $course_image;
    public $course_color;
    
    public $user_firstname;
    public $user_surname;
    
}
