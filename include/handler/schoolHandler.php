<?php

class SchoolHandler extends Handler {
    
    public $school;
    public $this_school_rights;
    public $school_types;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_school_types() {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            $query = "SELECT * FROM school_type";
            
            $this->school_types = DbHandler::get_instance()->return_query($query);
            
            if (count($this->school_types) == 0) {
                throw new Exception ("NO_SCHOOL_TYPES_FOUND");
            } 
            
            return true;
    } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function get_school_rights ($school_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            
            $this->verify_school_exists($school_id);
            
            $query = "SELECT * FROM school_rights WHERE school_id = :school_id";
            $school_rights_id_array = DbHandler::get_instance()->return_query($query, $school_id);
            
            if (count($school_rights_id_array) == 0) {
                throw new Exception ("NO_RIGHTS_FOUND_FOR_THIS_SCHOOL");
            } 
            
            $query_rights = "SELECT * FROM rights WHERE id = :id";
            foreach ($school_rights_id_array as $rights_id) {
                $right = reset(DbHandler::get_instance()->return_query($query_rights, $rights_id["rights_id"]));
                $this->this_school_rights[] = $right["prefix"];
            }
            return true;
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }    
    
    public function update_school_rights_by_id($school_id, $array_of_rights_prefixes) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->verify_school_exists($school_id);
            if (!$this->delete_school_rights_by_school_id($school_id)) {
                return false;
            }
            
            if ($this->assign_school_rights_by_school_id($school_id, $array_of_rights_prefixes)) {
                return true;
            }
            
            return false;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function assign_school_rights_by_school_id($school_id, $array_of_rights_prefixes) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->verify_array_contains_strings($array_of_rights_prefixes);
            $query_rights_id = "SELECT id FROM rights WHERE prefix in (";
            if (empty($array_of_rights_prefixes)) {
                throw new Exception ("RIGHTS_ARRAY_IS_EMPTY");
            }
            
            foreach ($array_of_rights_prefixes as $value) {
                $query_rights_id .= "'" . $value . "', "; 
            }
            
            $query_rights_id = rtrim($query_rights_id, ', ');
            $query_rights_id .= ")";
            $rights_id_array = DbHandler::get_instance()->return_query($query_rights_id);
            $query_assign_rights = "INSERT INTO school_rights (school_id, rights_id) VALUES (:school_id, :rights_id)";
            
            foreach ($rights_id_array as $value) {
                DbHandler::get_instance()->query($query_assign_rights, $school_id, $value['id']);
            }
            
            $this->get_school_rights($school_id);
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    private function delete_school_rights_by_school_id($school_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->verify_school_exists($school_id);
            
            $query = "DELETE FROM school_rights WHERE school_id = :school_id";
            
            if (DbHandler::get_instance()->query($query, $school_id)) {
                return true;
            } else {
                throw new Exception ("SCHOOL_RIGHTS_COULD_NOT_BE_DELETED_UNKNOWN_ERROR");
            }
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
        
    public function delete_school_by_id ($id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->verify_school_exists($id);
            
            $query = "DELETE FROM school WHERE id = :id";
            if (DbHandler::get_instance()->query($query, $id)) {
                $this->delete_school_rights_by_school_id($id);
                return true;
            } else {
                throw new Exception ("SCHOOL_COULD_NOT_BE_DELETED_UNKNOWN_ERROR");
            }
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function get_school_by_id ($id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->verify_school_exists($id);
            
            $query = "SELECT * FROM school INNER JOIN school_type ON school.school_type_id = school_type.id WHERE school.id = :id LIMIT 1";
            $this->school = new School(reset(DbHandler::get_instance()->return_query($query, $id)));
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function update_school_by_id ($id, $name, $phone, $address, $email, $school_type_id, $max_students, $subscription_end) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->verify_school_exists($id);
            $this->verify_name($name);
            $this->verify_phone($phone);
            $this->verify_address($address);
            $this->verify_email($email);
            $this->verify_school_type($school_type_id);
            $this->verify_max_students($max_students);
            $this->verify_subscription_end($subscription_end);
            
            $query = "UPDATE school SET name=:name, phone=:phone, address=:address, email=:email, school_type_id=:school_type_id, max_students=:max_students, subscription_end=:subscription_end WHERE id = :id";
            
            if (DbHandler::get_instance()->query($query, $name, $phone, $address, $email, $school_type_id, $max_students, $subscription_end, $id)) {
                return true;
            } else {
                throw new Exception ("SCHOOL_NOT_UPDATED_UNKNOWN_ERROR");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function create_school_step_one ($name, $phone, $address, $email, $school_type_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
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
            
            $this->school = new School($school_array);
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function create_school_step_two ($school, $max_students, $subscription_end) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->is_null_or_empty($school);
            $this->verify_max_students($max_students);
            $this->verify_subscription_end($subscription_end);
            
            $school->max_students = $max_students;
            $school->subscription_end = $subscription_end;
            
            if (!$this->create_school($school)) {
                throw new Exception ("SCHOOL_CREATION_FAILED_UNKNOWN_ERROR");
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function create_school_step_three ($array_of_rights) {
        try {
            if (!$this->user_exists()) {
                throw new Exception ("USER_NOT_LOGGED_IN");
            }
            $this->is_null_or_empty($array_of_rights);
            
            if ($this->assign_school_rights_by_school_id($this->school->id, $array_of_rights)) {
                return true;
            }
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    private function verify_array_contains_strings($array_of_strings) {
        foreach ($array_of_strings as $value) {
            if (!is_string($value)) {
                throw new Exception ("ARRAY_IS_NOT_STRINGS");
            }
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
        
        $ds = strtotime($subscription_end);
        $ts = strtotime(date($format));
        if ($ts > $ds) {
            throw new Exception ("SUBSCRIPTION_END_INVALID");
        }
    }
    
    private function verify_max_students($max_students) {
        if (!is_int($max_students)) {
            throw new Exception ("MAX_STUDENTS_HAS_INVALID_NUMBER");
        }
        $this->is_null_or_empty($max_students);
    }
    
    private function verify_school_exists($id) {
        if (!is_int($id)) {
            throw new Exception ("INVALID_INPUT_IS_NOT_INT");
        }
        
        $count = DbHandler::get_instance()->count_query("SELECT * FROM school WHERE id = :id", $id);
        if (!($count == 1)) {
            throw new Exception ("NO_SCHOOLS_FOUND_WITH_THIS_ID");
        } 
    }
    
    private function verify_school_type($school_type_id) {
        if (!is_int($school_type_id)) {
            throw new Exception ("WRONG_SCHOOL_TYPE_ID");
        }
        $this->is_null_or_empty($school_type_id);
        
        $count = DbHandler::get_instance()->count_query("SELECT * FROM school_type WHERE id = :id", $school_type_id);
        if (!($count == 1)) {
            throw new Exception ("WRONG_SCHOOL_TYPE_ID");
        }
    }
    
    private function verify_address($address) {
        $this->is_null_or_empty($address);
    }
    
    private function verify_phone ($phone) {
        $this->is_null_or_empty($phone);
    }
    
    private function verify_email ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception ("EMAIL_HAS_WRONG_FORMAT");
        }
    }
    
    private function verify_name ($name) {
        $this->is_null_or_empty($name);
    }
    
    private function is_null_or_empty($var) {
        if (empty($var)) {
            throw new Exception ("OBJECT_IS_EMPTY");
        }

        if (!isset($var)) {
            throw new Exception ("OBJECT_DOESNT_EXIST");
        }
    }
    
    private function create_school ($school) {
        $query = "INSERT INTO school (name, address, school_type_id, phone, email, max_students, subscription_end) "
                . "VALUES (:name, :address, :school_type_id, :phone, :email, :max_students, :subscription_end)";
        $executedQuery = DbHandler::get_instance()->query($query, $school->name, $school->address, $school->school_type_id, $school->phone, $school->email, $school->max_students, $school->subscription_end);
        $this->school->id = DbHandler::get_instance()->last_inserted_id();
        if ($executedQuery) {
            return true;
        } else {
            
        }
    }
}

