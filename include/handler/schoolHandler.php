<?php

class SchoolHandler extends Handler {
    
    public $school;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function delete_school_by_id ($id) {
        try {
            $this->verify_school_exists($id);
            
            $query = "DELETE FROM school WHERE id = :id";
            if (DbHandler::get_instance()->Query($query, $id)) {
                return true;
            } else {
                throw new Exception ("SCHOOL_COULD_NOT_BE_DELETED_UNKNOWN_ERROR");
            }
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::ReturnError($exc->getMessage());
            return false;
        }
    }
    
    public function get_school_by_id ($id) {
        try {
            $this->verify_school_exists($id);
            
            $query = "SELECT * FROM school WHERE id = :id LIMIT 1";
            $this->school = reset(DbHandler::get_instance()->ReturnQuery($query, $id));
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::ReturnError($exc->getMessage());
            return false;
        }
    }
    
    public function update_school_by_id ($id, $name, $phone, $address, $email, $school_type_id, $max_students, $subscription_end) {
        try {
            $this->verify_school_exists($id);
            $this->verify_name($name);
            $this->verify_phone($phone);
            $this->verify_address($address);
            $this->verify_email($email);
            $this->verify_school_type($school_type_id);
            $this->verify_max_students($max_students);
            $this->verify_subscription_end($subscription_end);
            
            $query = "UPDATE school SET name=:name, phone=:phone, address=:address, email=:email, school_type_id=:school_type_id, max_students=:max_students, subscription_end=:subscription_end WHERE id = :id";
            
            if (DbHandler::get_instance()->Query($query, $name, $phone, $address, $email, $school_type_id, $max_students, $subscription_end, $id)) {
                return true;
            } else {
                throw new Exception ("SCHOOL_NOT_UPDATED_UNKNOWN_ERROR");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::ReturnError($exc->getMessage());
            return false;
        }
    }
    
    public function create_school_step_one ($name, $phone, $address, $email, $school_type_id) {
        try {
            $this->verify_name($name);
            $this->verify_phone($phone);
            $this->verify_address($address);
            $this->verify_email($email);
            $this->verify_school_type($school_type_id);
            
            $school_array = array(
                "name" => $name,
                "phone" => $phone,
                "address" => $address,
                "email" => $email,
                "school_type_id" => $school_type_id
            );
            
            $this->school = new school($school_array);
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::ReturnError($exc->getMessage());
        }
    }
    
    public function create_school_step_two ($school, $max_students, $subscription_end) {
        try {
            if (!$this->is_null_or_empty($school)) {
                throw new Exception ("SCHOOL_OBJECT_IS_EMPTY");
            }
            
            $this->verify_max_students($max_students);
            $this->verify_subscription_end($subscription_end);
            
            $school->max_students = $max_students;
            $school->subscription_end = $subscription_end;
            
            if ($this->create_school($school)) {
                return true;
            } else {
                throw new Exception ("SCHOOL_CREATION_FAILED_UNKNOWN_ERROR");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::ReturnError($exc->getMessage());
            return false;
        }
    }
    
    private function verify_subscription_end($subscription_end) {
        // checks valid date
        $format = "Y-m-d H:i:s";
        $d = date_parse_from_format($format, $subscription_end);
        if (!checkdate($d['month'], $d['day'], $d['year'])) {
            throw new Exception ("SUBSCRIPTION_END_INVALID");
        }
        // checks if date is in future or not
        
        $today = getdate();
        if ($today > $d) {
            throw new Exception ("SUBSCRIPTION_END_INVALID");
        }
    }
    
    private function verify_max_students($max_students) {
        if (!is_int($max_students) || !$this->is_null_or_empty($max_students)) {
            throw new Exception ("MAX_STUDENTS_HAS_INVALID_NUMBER");
        }
    }
    
    private function verify_school_exists($id) {
        if (!is_int($id)) {
            throw new Exception ("INVALID_INPUT_IS_NOT_INT");
        }
        
        $count = DbHandler::get_instance()->CountQuery("SELECT * FROM school WHERE id = :id", $id);
        if (!($count == 1)) {
            throw new Exception ("NO_SCHOOLS_FOUND_WITH_THIS_ID");
        } 
    }
    
    private function verify_school_type($school_type_id) {
        if (!is_int($school_type_id) || !$this->is_null_or_empty($school_type_id)) {
            throw new Exception ("WRONG_SCHOOL_TYPE_ID");
        }
        
        $count = DbHandler::get_instance()->CountQuery("SELECT * FROM school_type WHERE id = :id", $school_type_id);
        if (!($count == 1)) {
            throw new Exception ("WRONG_SCHOOL_TYPE_ID");
        }
    }
    
    private function verify_address($address) {
        if (!$this->is_null_or_empty($address)) {
            throw new Exception ("ADDRESS_IS_NULL_OR_EMPTY");
        }
    }
    
    private function verify_phone ($phone) {
        if (!$this->is_null_or_empty($phone)) {
            throw new Exception ("PHONE_IS_NULL_OR_EMPTY");
        }
    }
    
    private function verify_email ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception ("EMAIL_HAS_WRONG_FORMAT");
        }
    }
    
    private function verify_name ($name) {
        if (!$this->is_null_or_empty($name)) {
            throw new Exception ("NAME_IS_NULL_OR_EMPTY");
        }
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
    
    private function create_school ($school) {
        $query = "INSERT INTO school (name, address, school_type_id, phone, email, max_students, subscription_end) "
                . "VALUES (:name, :address, :school_type_id, :phone, :email, :max_students, :subscription_end)";
        $executedQuery = DbHandler::get_instance()->Query($query, $school->name, $school->address, $school->school_type_id, $school->phone, $school->email, $school->max_students, $school->subscription_end);
        if ($executedQuery) {
            return true;
        }
    }
}

