<?php

class ClassHandler extends Handler {

    public $school_class;
    public $classes = array();
    public $years;
    public $format = "Y-m-d";
    public $soon_expiring_classes;

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
            $this->school_class->remaining_days = $this->set_remaining_days($this->school_class);
            $this->school_class->total_days = $this->set_total_days($this->school_class);
            $this->school_class->number_of_students = $this->get_number_of_students_in_class($class_id);
            $this->school_class->number_of_teachers = $this->get_number_of_teachers_in_class($class_id);

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
            $query = "SELECT class.id, class.title, class.description, class_year.year as class_year,
                            class.start_date, class.end_date, class.open, class.school_id, school.name as school_name
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN school ON class.school_id = school.id ";

            switch ($this->_user->user_type_id) {
                case 1:
                    $array = DbHandler::get_instance()->return_query($query);
                    break;
                case 2:
                    $query .= " WHERE school.id = :school_id";
                    $array = DbHandler::get_instance()->return_query($query, $this->_user->school_id);
                    break;
                case 3: case 4:
                    $query .= " INNER JOIN user_class ON class.id = user_class.class_id WHERE school.id = :school_id AND user_class.users_id = :user_id";
                    $array = DbHandler::get_instance()->return_query($query, $this->_user->school_id, $this->_user->id);
                    break;
                default:
                    break;
            }
            unset($this->classes);
            $this->classes = array();
            foreach ($array as $value) {
                $class = new School_Class($value);
                $class->number_of_students = $this->get_number_of_students_in_class($class->id);
                $class->number_of_teachers = $this->get_number_of_teachers_in_class($class->id);
                $class->remaining_days = $this->set_remaining_days($class);
                $class->total_days = $this->set_total_days($class);
                $this->classes[] = $class;
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_soon_expiring_classes($school_id = 0, $number_of_classes = 5) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("CLASS_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            if (!is_numeric($number_of_classes)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            }
            if ($this->_user->user_type_id == 3 || $this->_user->user_type_id == 4) {
                $query = "SELECT class.* FROM class INNER JOIN user_class ON class.id = user_class.class_id WHERE end_date > curdate() AND open = 1 AND user_class.users_id = :user_id ";
            } else {
                $query = "SELECT class.* FROM class WHERE end_date > curdate() AND open = 1 ";
            }
            if ($school_id != 0) {
                $this->verify_school_exists($school_id);
                $query .= "AND school_id = :school_id ORDER BY end_date LIMIT " . $number_of_classes;
                if ($this->_user->user_type_id == 3 || $this->_user->user_type_id == 4) {
                    $data = DbHandler::get_instance()->return_query($query, $this->_user->id, $school_id);
                } else {
                    $data = DbHandler::get_instance()->return_query($query, $school_id);
                }
            } else {
                $query .= "ORDER BY end_date LIMIT " . $number_of_classes;
                if ($this->_user->user_type_id == 3 || $this->_user->user_type_id == 4) {

                    $data = DbHandler::get_instance()->return_query($query, $this->_user->id);
                } else {

                    $data = DbHandler::get_instance()->return_query($query);
                }
            }
            $this->soon_expiring_classes = [];
            foreach ($data as $value) {
                $class = new School_Class($value);
                $class->remaining_days = $this->set_remaining_days($class);
                $class->total_days = $this->set_total_days($class);
                $this->soon_expiring_classes[] = $class;
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
            $this->verify_school_exists($school_id);

            $query = "SELECT class.id, class.title, class.description, class_year.year as class_year,
                            class.start_date, class.end_date, class.open
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            WHERE class.school_id = :class_id";

            if ($is_open) {
                $query .= " AND class.open = 1 AND class.start_date <= curdate() AND class.end_date >= curdate()";
            }


            $array = DbHandler::get_instance()->return_query($query, $school_id);
            unset($this->classes);
            $this->classes = array();
            foreach ($array as $value) {
                $class = new School_Class($value);
                $class->remaining_days = $this->set_remaining_days($class);
                $class->total_days = $this->set_total_days($class);
                $class->number_of_students = $this->get_number_of_students_in_class($class->id);
                $class->number_of_teachers = $this->get_number_of_teachers_in_class($class->id);
                $this->classes[] = $class;
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

            $query = "SELECT class.*, class_year.year as class_year
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN user_class on class.id = user_class.class_id
                            WHERE user_class.users_id = :user_id";

            $array = DbHandler::get_instance()->return_query($query, $user_id);
            unset($this->classes);
            $this->classes = array();
            foreach ($array as $value) {
                $class = new School_Class($value);
                $class->remaining_days = $this->set_remaining_days($class);
                $class->total_days = $this->set_total_days($class);
                $class->number_of_students = $this->get_number_of_students_in_class($class->id);
                $class->number_of_teachers = $this->get_number_of_teachers_in_class($class->id);
                $this->classes[] = $class;
            }
            var_dump($this->classes);
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

            $this->verify_title($title);
            $this->verify_school_exists($school_id);
            if ($class_start == "") {
                $class_start = date($this->format);
            }
            $this->verify_start_date($class_start);
            $this->verify_end_date($class_end);
            $year_id = $this->get_year_id($class_end);
            $this->verify_start_date_is_lower_than_end_date($class_start, $class_end);
            $query = "INSERT INTO class (title, description, class_year_id, school_id, start_date, end_date, open)
                        VALUES (:title, :description, :class_year_id, :school_id, :start_date, :end_date, :open)";
            if (DbHandler::get_instance()->query($query, $title, $description, $year_id, $school_id, $class_start, $class_end, $class_open)) {
                return true;
            }
            return false;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function add_user_to_class($array_of_user_ids_or_single_id, $class_id) {
        try {

            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("CLASS_ASSIGN_USER")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_class_exists($class_id);
            $query = "INSERT INTO user_class (users_id, class_id) VALUES (:user_id, :class_id)";

            if (is_array($array_of_user_ids_or_single_id) && !empty($array_of_user_ids_or_single_id)) {
                foreach ($array_of_user_ids_or_single_id as $value) {
                    DbHandler::get_instance()->query($query, $value, $class_id);
                }
            } elseif (is_numeric($array_of_user_ids_or_single_id)) {
                DbHandler::get_instance()->query($query, $array_of_user_ids_or_single_id, $class_id);
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function remove_user_from_class($array_of_user_ids_or_single_id, $class_id) {
        try {

            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("CLASS_ASSIGN_USER")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            $this->verify_class_exists($class_id);


            if (is_array($array_of_user_ids_or_single_id) && !empty($array_of_user_ids_or_single_id)) {
                $query = "DELETE FROM user_class WHERE class_id = :class_id AND users_id IN (" . generate_in_query($array_of_user_ids_or_single_id) . ")";

                DbHandler::get_instance()->query($query, $class_id);

            } elseif (is_numeric($array_of_user_ids_or_single_id)) {
                DbHandler::get_instance()->query("DELETE FROM user_class WHERE users_id = :user_id AND class_id = :class_id", $array_of_user_ids_or_single_id, $class_id);
            } else {
                throw new Exception("OBJECT_IS_EMPTY");
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function update_class_open($class_id) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("CLASS_EDIT")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            $this->verify_class_exists($class_id);

            $query = "UPDATE class SET open= NOT open WHERE id=:id";

            if (!DbHandler::get_instance()->query($query, $class_id)) {
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
            $this->verify_title($title);
            $this->verify_end_date($class_end);
            $this->verify_start_date($class_start);
            $this->verify_start_date_is_lower_than_end_date($class_start, $class_end);
            $this->verify_school_exists($school_id);
            $this->verify_class_open($class_open);

            $year_id = $this->get_year_id($class_end);
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

            $delete_queries = array();
            $delete_queries[] = "DELETE FROM class where id = :id";
            $delete_queries[] = "DELETE FROM user_class WHERE class_id = :id";

            foreach($delete_queries as $value)
            {
                if(!DbHandler::get_instance()->query($value, $class_id)) {
                    throw new Exception("DEFAULT");
                }
            }
            
            $this->delete_class_homework($class_id);
            
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    private function delete_class_homework($class_id)
    {
        $class_homework = DbHandler::get_instance()->return_query("SELECT * FROM class_homework WHERE class_id = :id", $class_id);
        
        foreach($class_homework as $value)
        {
            if(DbHandler::get_instance()->count_query("SELECT id FROM class_homework WHERE homework_id = :homework AND class_id != :class_id", $value['homework_id'], $class_id) < 1)
            {
                DbHandler::get_instance()->query("DELETE FROM homework WHERE id = :homework_id", $value['homework_id']);
                DbHandler::get_instance()->query("DELETE FROM homework_lecture WHERE homework_id = :homework_id", $value['homework_id']);
                DbHandler::get_instance()->query("DELETE FROM homework_test WHERE homework_id = :homework_id", $value['homework_id']);
            }
            DbHandler::get_instance()->query("DELETE FROM class_homework WHERE homework_id = :homework_id AND class_id = :class_id", $value['homework_id'], $class_id);
        }
    }

    private function set_remaining_days($class) {
        if ($class->open == "1" && (strtotime($class->end_date) > strtotime(date($this->format)))) {
            return date_diff(date_create_from_format($this->format, $class->end_date), date_create_from_format($this->format, date($this->format)))->format("%a");
        } else {
            return 0;
        }
    }

    private function set_total_days($class) {
        return date_diff(date_create_from_format($this->format, $class->end_date), date_create_from_format($this->format, $class->start_date))->format("%a");
    }

    private function get_number_of_students_in_class($class_id) {
        $query = "SELECT user_class.id FROM user_class
                 INNER JOIN users ON user_class.users_id = users.id
                    WHERE class_id = :id AND users.user_type_id = 4";
        $count = DbHandler::get_instance()->count_query($query, $class_id);
        return $count;
    }

    private function get_number_of_teachers_in_class($class_id) {
        $query = "SELECT user_class.id FROM user_class
                 INNER JOIN users ON user_class.users_id = users.id
                    WHERE class_id = :id AND users.user_type_id = 3";
        $count = DbHandler::get_instance()->count_query($query, $class_id);
        return $count;
    }

    private function get_year_id($date) {
        $d = date_parse_from_format($this->format, $date);
        $year = $d['year'];
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

    private function verify_title($title) {
        $this->is_null_or_empty($title);
    }

    private function verify_class_open($class_open) {
        if (!is_numeric($class_open)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        if (!($class_open == "1" || $class_open == "0")) {
            throw new Exception("ARGUMENT_NOT_BOOL");
        }
    }

    private function verify_start_date_is_lower_than_end_date($start_date_string, $end_date_string) {
        $ds = strtotime($start_date_string);
        $de = strtotime($end_date_string);

        if ($ds > $de) {
            throw new Exception("START_DATE_MUST_BE_LOWER_THAN_END");
        }
    }

    private function verify_start_date($start_date) {
        $this->verify_is_date($start_date);
    }

    private function verify_end_date($end_date) {
        $this->verify_is_date($end_date);
    }

    private function verify_is_date($date) {
        $d = date_parse_from_format($this->format, $date);

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
