<?php

class SchoolHandler {
//    
//    public function __construct() {
//        parent::__construct();
//    }
    
    public function create_school_step_one ($name, $phone, $address, $email, $school_type_id) {
        try {
            if (!$this->verify_name($name)) {
                throw new Exception ("NAME_IS_NULL_OR_EMPTY");
            }
            
            if (!$this->verify_phone($phone)) {
                throw new Exception ("PHONE_IS_NULL_OR_EMPTY");
            }
            
            if (!$this->verify_address($address)) {
                throw new Exception ("ADDRESS_IS_NULL_OR_EMPTY");
            }
            
            if (!$this->verify_email($email)) {
                throw new Exception ("EMAIL_HAS_WRONG_FORMAT");
            }
            
            if (!$this->verify_school_type($school_type_id)) {
                throw new Exception ("WRONG_SCHOOL_TYPE_ID");
            }
            
            $school_array = array(
                "name" => $name,
                "phone" => $phone,
                "address" => $address,
                "email" => $email,
                "school_type_id" => $school_type_id
            );
            
            $school = new school($school_array);
            return $school;
        } catch (Exception $exc) {
            ErrorHandler::ReturnError($exc->getMessage());
        }
    }
    
    public function create_school_step_two ($school, $max_students, $subscription_end) {
        try {
            if (!$this->is_null_or_empty($school)) {
                throw new Exception ("SCHOOL_OBJECT_IS_EMPTY");
            }
            if (!$this->verify_max_students($max_students)) {
                throw new Exception ("MAX_STUDENTS_HAS_INVALID_NUMBER");
            }
            if (!$this->verify_subscription_end($subscription_end)) {
                throw new Exception ("SUBSCRIPTION_END_INVALID");
            }
            $school->max_students = $max_students;
            $school->subscription_end = $subscription_end;
            
            $this->create_school($school);
        } catch (Exception $exc) {
            ErrorHandler::ReturnError($exc->getMessage());
        }
    }
    
    private function verify_subscription_end($subscription_end) {
        // checks valid date
        $format = "Y-m-d H:i:s";
        $d = date_parse_from_format($format, $subscription_end);
        if (!checkdate($d['month'], $d['day'], $d['year'])) {
            return false;
        }
        // checks if date is in future or not
        
        $today = getdate();
        if ($today > $d) {
            return false;
        }
        return true;
    }
    
    private function verify_max_students($max_students) {
        if (!is_int($max_students) || !$this->is_null_or_empty($max_students)) {
            return false;
        }
        return true;
    }
    
    private function verify_school_type($school_type_id) {
        if (!is_int($school_type_id) || !$this->is_null_or_empty($school_type_id)) {
            return false;
        }
        
        $count = DbHandler::getInstance()->CountQuery("SELECT id FROM school_type WHERE id = :id", $school_type_id);
        if ($count == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    private function verify_address($address) {
        if (!$this->is_null_or_empty($address)) {
            return false;
        }
        
        return true;
    }
    
    private function verify_phone ($phone) {
        if (!$this->is_null_or_empty($phone)) {
            return false;
        }
        
        return true;
    }
    
    private function verify_email ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return true;
    }
    
    private function verify_name ($name) {
        if (!$this->is_null_or_empty($name)) {
            return false;
        }
        
        return true;
    }
    
    private function is_null_or_empty($var) {
        if (empty($var)) {
            return false;
        }

        if (!isset($var)) {
            return false;
        }
        return true;
    }
    
    private function create_school ($school) 
    {
        $query = "INSERT INTO school (name, address, school_type_id, phone, email, max_students, subscription_end) "
                . "VALUES (:name, :address, :school_type_id, :phone, :email, :max_students, :subscription_end);";
        $executedQuery = DbHandler::getInstance()->Query($query, $school->name, $school->address, $school->school_type_id, $school->phone, $school->email, $school->max_students, $school->subscription_end);
        if ($executedQuery) {
            echo 'School created successfully';
        }
    }
}

