<?php

class SchoolHandler extends Handler {

    public $school;
    public $all_schools;
    public $this_school_rights;
    public $school_types;
    public $test;
    public $open_slots;
    public $soon_expiring_schools;
    public $format = "Y-m-d";

    public function __construct() {
        parent::__construct();
    }

    public function get_all_schools($is_open = false) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $query = "SELECT school.id as id, school.name as name, school.address, school.zip_code, school.city, school.school_type_id, school.phone, 
                     school.email, school.max_students, school.subscription_start, school.subscription_end, school_type.title as school_type, school.open
                     FROM school 
                     INNER JOIN school_type ON school.school_type_id = school_type.id";

            if ($is_open) {
                $query .= " AND subscription_end >= curdate() AND subscription_start <= curdate()";
            }
            $this->all_schools = [];
            $schools = DbHandler::get_instance()->return_query($query);
            foreach ($schools as $value) {
                $school = new School($value);
                $school->remaining_days = $this->set_remaining_days($school);
                $this->all_schools[] = $school;
            }

            if (count($this->all_schools) == 0) {
                throw new Exception("NO_SCHOOL_FOUND");
            }
            $school_ids = [];
            foreach ($this->all_schools as $value) {
                $school_ids[] = $value->id;
            }
            $students_max_all_schools = $this->get_current_students_for_all_schools($school_ids);
            foreach ($students_max_all_schools as $value) {
                foreach ($this->all_schools as $key => $value_nested) {
                    if ($value['school_id'] == $value_nested->id) {
                        $this->all_schools[$key]->current_students = $value['current_students'];
                    }
                }
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_school_types() {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            $query = "SELECT * FROM school_type";

            $this->school_types = DbHandler::get_instance()->return_query($query);

            if (count($this->school_types) == 0) {
                throw new Exception("NO_SCHOOL_TYPES_FOUND");
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function delete_school_by_id($id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_DELETE")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_school_exists($id);

            $query = "DELETE FROM school WHERE id = :id";
            if (DbHandler::get_instance()->query($query, $id)) {
                $this->delete_school_rights_by_school_id($id);
                return true;
            } else {
                throw new Exception("SCHOOL_COULD_NOT_BE_DELETED_UNKNOWN_ERROR");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_school_by_id($id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            $this->verify_user_school_access($id);
            $this->verify_school_exists($id);

            $query = "SELECT school.id as id, school.name as name, school.address, school.zip_code, school.city, school.school_type_id, school.phone, 
                     school.email, school.max_students, school.subscription_start, school.subscription_end, school_type.title as school_type, school.open 
                     FROM school INNER JOIN school_type ON school.school_type_id = school_type.id WHERE school.id = :id LIMIT 1";
            $this->school = new School(reset(DbHandler::get_instance()->return_query($query, $id)));
            $this->school->remaining_days = $this->set_remaining_days($this->school);
            $id = [];
            $id[] = $this->school->id;
            $current_students = $this->get_current_students_for_all_schools($id);
            $this->school->current_students = !empty($current_students) ? $current_students[0]['current_students'] : 0;
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function update_school_by_id($id, $name, $phone, $address, $zip_code, $city, $email, $school_type_id, $max_students, $subscription_start, $subscription_end) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_EDIT")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_school_exists($id);
            $this->verify_name($name);
            $this->verify_phone($phone);
            $this->verify_address($address);
            $this->verify_zip_code($zip_code);
            $this->verify_city($city);
            $this->verify_email($email);
            $this->verify_school_type($school_type_id);
            $this->verify_max_students($max_students);

            $this->verify_subscription_start($subscription_start);
            $this->verify_subscription_end($subscription_end);
            $this->verify_start_date_is_lower_than_end_date($subscription_start, $subscription_end);

            $query = "UPDATE school SET name=:name, phone=:phone, address=:address, zip_code=:zip_code, city=:city, email=:email, "
                    . "school_type_id=:school_type_id, max_students=:max_students, subscription_start=:subscription_start, subscription_end=:subscription_end WHERE id = :id";

            if (DbHandler::get_instance()->query($query, $name, $phone, $address, $zip_code, $city, $email, $school_type_id, $max_students, $subscription_start, $subscription_end, $id)) {
                return true;
            } else {
                throw new Exception("SCHOOL_NOT_UPDATED_UNKNOWN_ERROR");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function create_school_step_one($name, $phone, $address, $zip_code, $city, $email, $school_type_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_CREATE")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_name($name);
            $this->verify_phone($phone);
            $this->verify_address($address);
            $this->verify_zip_code($zip_code);
            $this->verify_city($city);
            $this->verify_email($email);
            $this->verify_school_type($school_type_id);

            $school_array = array(
                "name" => $name,
                "phone" => $phone,
                "address" => $address,
                "zip_code" => $zip_code,
                "city" => $city,
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

    public function create_school_step_two($school, $max_students, $subscription_start, $subscription_end) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_CREATE")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->is_null_or_empty($school);
            $this->verify_max_students($max_students);
            $this->verify_subscription_start($subscription_start);
            $this->verify_subscription_end($subscription_end);
            $this->verify_start_date_is_lower_than_end_date($subscription_start, $subscription_end);
            $school->max_students = $max_students;
            $school->subscription_start = $subscription_start;
            $school->subscription_end = $subscription_end;

            if (!$this->create_school($school)) {
                throw new Exception("SCHOOL_CREATION_FAILED_UNKNOWN_ERROR");
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function update_open_state($school_id) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_EDIT")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            $this->verify_school_exists($school_id);

            $query = "UPDATE school SET open = NOT open WHERE id=:id;";

            if (DbHandler::get_instance()->query($query, $school_id)) {
                return true;
            } else {
                throw new Exception("DEFAULT");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function create_school_step_three($array_of_rights) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_SET_RIGHTS")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
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

    public function can_add_students($school_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            if (empty($school_id)) {
                throw new Exception("CREATE_NO_SCHOOL");
            }

            $this->verify_user_school_access($school_id);
            $this->student_slots_open($school_id);

            if ($this->open_slots < 1) {
                throw new Exception("SCHOOL_NO_SLOTS");
            }

            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function student_slots_open($school_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            if (empty($school_id)) {
                throw new Exception("CREATE_NO_SCHOOL");
            }

            $this->verify_user_school_access($school_id);
            $this->verify_school_exists($school_id);
            $active_students = DbHandler::get_instance()->count_query("SELECT id FROM users WHERE school_id = :school AND open = 1", $school_id);
            $max_students = reset(DbHandler::get_instance()->return_query("SELECT max_students FROM school WHERE id = :school_id", $school_id));
            $this->open_slots = $max_students["max_students"] - $active_students;

            return true;
        } catch (Exception $ex) {

            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function school_has_classes($school_id, $class_ids) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            if (empty($school_id)) {
                throw new Exception("CREATE_NO_SCHOOL");
            }

            $this->verify_user_school_access($school_id);
            $this->verify_school_exists($school_id);

            if (!empty($class_ids)) {
                $this->verify_array_contains_numerics($class_ids);

                $query = "SELECT * FROM class WHERE school_id = :school_id AND id IN (";

                for ($i = 0; $i < count($class_ids); $i++) {
                    $query .= $i != 0 ? ", " : "";
                    $query .= "'" . $class_ids[$i] . "'";
                }

                $query .= ")";
                $count = DbHandler::get_instance()->count_query($query, $school_id);


                if ($count != count($class_ids)) {
                    throw new Exception("CLASS_NOT_FOUND");
                }
            }

            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function get_soon_expiring_schools($limit = 60) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!is_numeric($limit)) {
                throw new Exception("INVALID_INPUT");
            }
            if ($this->_user->user_type_id != 1) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $query = "SELECT * FROM school WHERE subscription_end BETWEEN curdate() AND curdate() + INTERVAL :limit DAY AND open = 1 ORDER BY subscription_end";
            $data = DbHandler::get_instance()->return_query($query, $limit);
            $this->soon_expiring_schools = [];
            foreach ($data as $value) {
                $school = new School($value);
                $school->remaining_days = $this->set_remaining_days($school);
                $this->soon_expiring_schools[] = $school;
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_current_students_for_all_schools($array_of_int) {
        if (!is_array($array_of_int)) {
            throw new Exception("INVALID_INPUT");
        }
        foreach ($array_of_int as $value) {
            if (!is_numeric($value)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            }
        }
        $in_q = generate_in_query($array_of_int);
        $array_students = DbHandler::get_instance()->return_query("SELECT COUNT(*) as current_students, school_id FROM users WHERE school_id IN (" . $in_q . ") AND open = 1 AND user_type_id IN (3,4)"
                . "GROUP BY school_id");
        $this->test = $in_q;
        return $array_students;
    }

    private function set_remaining_days($school) {
        if ($school->open == "1" && (strtotime($school->subscription_end) > strtotime(date($this->format)))) {
            return date_diff(date_create_from_format($this->format, $school->subscription_end), date_create_from_format($this->format, date($this->format)))->format("%a");
        } else {
            return 0;
        }
    }

    private function verify_user_school_access($school_id) {
        if (empty($this->_user->school_id) || $this->_user->school_id != $school_id) {
            if (!RightsHandler::has_user_right("SCHOOL_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
        }
    }

    private function verify_start_date_is_lower_than_end_date($start_date_string, $end_date_string) {

        $ds = strtotime($start_date_string);
        $de = strtotime($end_date_string);

        if ($ds > $de) {
            throw new Exception("START_DATE_MUST_BE_LOWER_THAN_END");
        }
    }

    private function verify_array_contains_strings($array_of_strings) {
        foreach ($array_of_strings as $value) {
            if (!is_string($value)) {
                throw new Exception("ARRAY_IS_NOT_STRINGS");
            }
        }
    }

    private function verify_array_contains_numerics($array_of_nums) {
        foreach ($array_of_nums as $value) {
            if (!is_numeric($value)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            }
        }
    }

    private function verify_is_date($date) {
        $d = date_parse_from_format($this->format, $date);
        if (!checkdate($d['month'], $d['day'], $d['year'])) {
            throw new Exception("SUBSCRIPTION_END_INVALID");
        }
    }

    private function verify_subscription_start($subscription_start) {
        $this->verify_is_date($subscription_start);
    }

    private function verify_subscription_end($subscription_end) {
        $this->verify_is_date($subscription_end);
        $end_date = date_parse_from_format($this->format, $subscription_end);
        $de = $end_date['year'] . "/" . $end_date['month'] . "/" . $end_date['day'];

        $ds = strtotime($de);
        $ts = strtotime(date($this->format));
        if ($ts > $ds) {
            throw new Exception("SUBSCRIPTION_END_INVALID");
        }
    }

    private function verify_max_students($max_students) {
        if (!is_numeric($max_students)) {
            throw new Exception("MAX_STUDENTS_HAS_INVALID_NUMBER");
        }
    }

    private function verify_school_exists($id) {
        if (!is_numeric($id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM school WHERE id = :id", $id);
        if (!($count == 1)) {
            throw new Exception("NO_SCHOOLS_FOUND_WITH_THIS_ID");
        }
    }

    private function verify_school_type($school_type_id) {
        if (!is_numeric($school_type_id)) {
            throw new Exception("WRONG_SCHOOL_TYPE_ID");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM school_type WHERE id = :id", $school_type_id);
        if (!($count == 1)) {
            throw new Exception("WRONG_SCHOOL_TYPE_ID");
        }
    }

    private function verify_zip_code($zip_code) {
        if (!is_numeric($zip_code)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
    }

    private function verify_city($city) {
        $this->is_null_or_empty($city);
    }

    private function verify_address($address) {
        $this->is_null_or_empty($address);
    }

    private function verify_phone($phone) {
        $this->is_null_or_empty($phone);
    }

    private function verify_email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("EMAIL_HAS_WRONG_FORMAT");
        }
    }

    private function verify_name($name) {
        $this->is_null_or_empty($name);
    }

    private function is_null_or_empty($var) {
        if (empty($var)) {
            throw new Exception("OBJECT_IS_EMPTY");
        }

        if (!isset($var)) {
            throw new Exception("OBJECT_DOESNT_EXIST");
        }
    }

    private function create_school($school) {
        $query = "INSERT INTO school (name, address, school_type_id, phone, email, max_students, subscription_start, subscription_end, zip_code, city, open) "
                . "VALUES (:name, :address, :school_type_id, :phone, :email, :max_students, :subscription_start,:subscription_end, :zip_code, :city, :open)";
        $executedQuery = DbHandler::get_instance()->query($query, $school->name, $school->address, $school->school_type_id, $school->phone, $school->email, $school->max_students, $school->subscription_start, $school->subscription_end, $school->zip_code, $school->city, 1);
        $this->school->id = DbHandler::get_instance()->last_inserted_id();
        if ($executedQuery) {
            return true;
        } else {
            return false;
        }
    }

}
