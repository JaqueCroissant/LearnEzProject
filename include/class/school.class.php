<?php

class School extends ORM{
    public $id;
    public $name;
    public $address;
    public $zip_code;
    public $city;
    public $school_type;
    public $school_type_id;
    public $phone;
    public $email;
    public $current_students = 0;
    public $max_students;
    public $subscription_start;
    public $subscription_end;
    public $open;
    public $remaining_days;
    
    public $classes = array();
}

?>