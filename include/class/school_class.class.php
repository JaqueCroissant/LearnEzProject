<?php

class School_Class extends ORM{
    public $id;
    public $title;
    public $description;
    public $class_year;
    public $start_date;
    public $end_date;
    public $open;
    public $school_id;
    public $school_name;
    public $number_of_students;
    public $number_of_teachers;
    public $remaining_days;
    
    public $homework = array();
}
