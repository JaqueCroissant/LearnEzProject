<?php

class ClassHandler extends Handler {

    public $school_class;
    public $classes;
    public $years;
    private $format = "Y-m-d H:i:s";

    public function __construct() {
        parent::__construct();
    }

    public function get_class_by_id($class_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            $this->verify_class_exists($class_id);

            $query = "SELECT class.id, class.title, class.description, class_year.year as class_year,
                            class.start_date, class.end_date, class.open, class.school_id, school.name as school_name
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id 
                            INNER JOIN school ON class.school_id = school.id
                            WHERE class.id = :id";

            $this->school_class = new School_Class(reset(DbHandler::get_instance()->return_query($query, $class_id)));

            if (empty($this->school_class)) {
                throw new Exception("OBJECT_IS_EMPTY");
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_all_classes() {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("CLASS_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $base_query = "SELECT class.id, class.title, class.description, class_year.year as class_year,
                            class.start_date, class.end_date, class.open, class.school_id, school.name as school_name
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN school ON class.school_id = school.id";

            switch ($this->_user->user_type_id) {
                case 1:
                    $array = DbHandler::get_instance()->return_query($base_query);

                    $this->classes = array();
                    foreach ($array as $value) {
                        $this->classes[] = new School_Class($value);
                    }
                    break;

                case 2: case 3:
                    $query = $base_query . " WHERE school.id = :school_id";

                    $array = DbHandler::get_instance()->return_query($query, $this->_user->school_id);

                    $this->classes = array();
                    foreach ($array as $value) {
                        $this->classes[] = new School_Class($value);
                    }
                    break;
                default:
                    break;
            }



            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_classes_by_school_id($school_id, $is_open = false) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("CLASS_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->is_null_or_empty($school_id);
            $this->verify_school_exists($school_id);

            $query = "SELECT class.id, class.title, class.description, class_year.year as class_year,
                            class.start_date, class.end_date, class.open
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            WHERE class.school_id = :class_id";

            if ($is_open) {
                $query .= " AND class.open = 1 AND class.start_date <= curdate() AND class.end_date >= curdate()";
            }


            $array = DbHandler::get_instance()->return_query($query, $school_id);

            $this->classes = array();
            foreach ($array as $value) {
                $this->classes[] = new School_Class($value);
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_classes_by_user_id($user_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            $this->verify_user_id($user_id);
            $this->verify_user_has_class($user_id);

            $query = "SELECT class.title, class.description, class_year.year as class_year,
                            class.start_date, class.end_date, class.open
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN user_class on class.id = user_class.class_id
                            WHERE user_class.users_id = :user_id";

            $array = DbHandler::get_instance()->return_query($query, $user_id);
            $this->classes = array();
            foreach ($array as $value) {
                $this->classes[] = new School_Class($value);
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function create_class($title, $school_id, $class_end, $description = null, $class_open = 1, $class_start = null) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("CLASS_CREATE")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            $this->is_null_or_empty($title);
            $this->verify_school_exists($school_id);
            if ($class_start == null) {
                $class_start = date($this->format);
            }
            $this->verify_start_date($class_start);
            $class_start = $class_start['year'] . '/' . $class_start['month'] . "/" . $class_start['day'];
            
            $year_id = $this->get_year_id($class_end);
            $this->verify_end_date($class_end);
            $class_end_string = $class_end['year'] . '/' . $class_end['month'] . '/' . $class_end['day'];
            $this->verify_start_date_is_lower_than_end_date($class_start, $class_end_string);
            $query = "INSERT INTO class (title, description, class_year_id, school_id, start_date, end_date, open)
                        VALUES (:title, :description, :class_year_id, :school_id, :start_date, :end_date, :open)";
            if (DbHandler::get_instance()->query($query, $title, $description, $year_id, $school_id, $class_start, $class_end_string, $class_open)) {
                return true;
            }
            return false;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function update_class_open($class_id, $open_int) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("CLASS_EDIT")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            if (!is_numeric($open_int)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            } elseif (!($open_int == "1" || $open_int == "0")) {
                throw new Exception("ARGUMENT_NOT_BOOL");
            }
            $this->verify_class_exists($class_id);

            $query = "UPDATE class SET open=:open WHERE id=:id";

            if (!DbHandler::get_instance()->query($query, $open_int, $class_id)) {
                throw new Exception("DEFAULT");
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function update_class($class_id, $title, $description, $class_open, $class_end, $class_start, $school_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("CLASS_EDIT")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_class_exists($class_id);
            $this->is_null_or_empty($title);
            $this->verify_end_date($class_end);
            $year_id = $this->get_year_id($class_end);
            $class_end = $class_end['year'] . '/' . $class_end['month'] . '/' . $class_end['day'];
            $this->verify_start_date($class_start);
            $class_start = $class_start['year'] . '/' . $class_start['month'] . "/" . $class_start['day'];
            $this->verify_start_date_is_lower_than_end_date($class_start, $class_end);
            $this->is_null_or_empty($school_id);
            $this->is_null_or_empty($class_open);
            if (!is_numeric($class_open)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            } elseif (!($class_open == "1" || $class_open == "0")) {
                throw new Exception("ARGUMENT_NOT_BOOL");
            }

            $query = "UPDATE class SET title=:title, description=:description, class_year_id=:year_id,
                        open=:open, start_date=:start_date, end_date=:end_date, school_id=:school_id WHERE id = :id";

            if (!DbHandler::get_instance()->query($query, $title, $description, $year_id, $class_open, $class_start, $class_end, $school_id, $class_id)) {
                throw new Exception("DEFAULT");
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function delete_class_by_id($class_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("CLASS_DELETE")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_class_exists($class_id);

            $query = "DELETE FROM class where id = :id";

            if (!DbHandler::get_instance()->query($query, $class_id)) {
                throw new Exception("DEFAULT");
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_year_and_year_prefix() {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            $q_year = "SELECT * FROM class_year";

            $this->years = DbHandler::get_instance()->return_query($q_year);
            if (count($this->years) == 0) {
                throw new Exception("CLASS_YEAR_NO_DATA");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_year_id($date) {
        $year = $date['year'];
        $year_id_array = reset(DbHandler::get_instance()->return_query("SELECT id FROM class_year WHERE year = :year LIMIT 1", $year));
        if (empty($year_id_array)) {
            throw new Exception("OBJECT_IS_EMPTY");
        }
        return $year_id_array['id'];
    }

    private function is_null_or_empty($var) {
        if (empty($var) && $var != "0") {
            throw new Exception("OBJECT_IS_EMPTY");
        }

        if (!isset($var)) {
            throw new Exception("OBJECT_DOESNT_EXIST");
        }
    }
    
    private function verify_start_date_is_lower_than_end_date($start_date_string, $end_date_string) {
        $ds = strtotime($start_date_string);
        $de = strtotime($end_date_string);
        
        if ($ds > $de) {
            throw new Exception ("START_DATE_MUST_BE_LOWER_THAN_END");
        }
    }

    private function verify_start_date($start_date) {
        $this->verify_is_date($start_date);
    }

    private function verify_end_date($end_date) {
        $this->verify_is_date($end_date);
    }

    private function verify_is_date($d) {
        if (!checkdate($d['month'], $d['day'], $d['year'])) {
            throw new Exception("SUBSCRIPTION_END_INVALID");
        }
    }

    private function verify_user_id($user_id) {
        if (!is_numeric($user_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
        $this->is_null_or_empty($user_id);
    }

    private function verify_school_exists($school_id) {
        if (!is_numeric($school_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM school WHERE id = :school_id", $school_id);
        if ($count == 0) {
            throw new Exception("NO_SCHOOLS_FOUND_WITH_THIS_ID");
        }
    }

    private function verify_user_has_class($user_id) {
        if (!is_numeric($user_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM user_class WHERE users_id = :id", $user_id);
        if ($count == 0) {
            throw new Exception("USER_HAS_NO_CLASS");
        }
    }

    private function verify_class_exists($class_id) {
        if (!is_numeric($class_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM class WHERE id = :id", $class_id);
        if (!($count == 1)) {
            throw new Exception("CLASS_NOT_FOUND");
        }
    }
}
