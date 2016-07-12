<?php

class ClassHandler extends Handler {

    public $school_class;
    public $classes_in_school;
    public $years;
    public $year_prefixes;
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

            $query = "SELECT class.title, class.description, class_year.year as class_year, translation_class_year_prefix.title as class_year_prefix,
                            class.start_date, class.end_date, class.open
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN class_year_prefix ON class.class_year_prefix_id = class_year_prefix.id
                            INNER JOIN translation_class_year_prefix ON class_year_prefix.id = translation_class_year_prefix.class_year_prefix_id
                            WHERE class.id = :id AND translation_class_year_prefix.language_id = :language_id";

            $this->school_class = new School_Class(reset(DbHandler::get_instance()->return_query($query, $class_id, TranslationHandler::get_current_language())));

            if (empty($this->school_class)) {
                throw new Exception("OBJECT_IS_EMPTY");
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_classes_by_school_id($school_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            $this->is_null_or_empty($school_id);

            $query = "SELECT class.id, class.title, class.description, class_year.year as class_year, translation_class_year_prefix.title as class_year_prefix,
                            class.start_date, class.end_date, class.open
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN class_year_prefix ON class.class_year_prefix_id = class_year_prefix.id
                            INNER JOIN translation_class_year_prefix ON class_year_prefix.id = translation_class_year_prefix.class_year_prefix_id
                            WHERE class.school_id = :class_id AND translation_class_year_prefix.language_id = :language_id";

            $array = DbHandler::get_instance()->return_query($query, $school_id, TranslationHandler::get_current_language());

            $this->classes_in_school = array();
            foreach ($array as $value) {
                $this->classes_in_school[] = new School_Class($value);
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
            $this->is_null_or_empty($user_id);
            $this->verify_user_has_class($user_id);

            $query = "SELECT class.title, class.description, class_year.year as class_year, translation_class_year_prefix.title as class_year_prefix,
                            class.start_date, class.end_date, class.open
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN class_year_prefix ON class.class_year_prefix_id = class_year_prefix.id
                            INNER JOIN translation_class_year_prefix ON class_year_prefix.id = translation_class_year_prefix.class_year_prefix_id
                            INNER JOIN user_class on class.id = user_class.class_id
                            WHERE user_class.users_id = :user_id AND translation_class_year_prefix.language_id = :language_id";

            $array = DbHandler::get_instance()->return_query($query, $user_id, TranslationHandler::get_current_language());
            foreach ($array as $value) {
                $this->classes_in_school[] = new School_Class($value);
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
            
            $this->is_null_or_empty($title);
            $this->is_null_or_empty($school_id);
            if ($class_start == null) {
                $class_start = date($this->format);
            } else {
                $class_start = $class_start['year'] . '/' . $class_start['month'] . "/" . $class_start['day'];
            }
            $year_id = $this->get_year_id($class_end);
            $class_end_string = $class_end['year'] . '/' . $class_end['month'] . '/' . $class_end['day'];
            $this->verify_class_end_is_greater($class_end_string, $class_start);
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

    public function update_class($class_id, $title, $description, $year, $year_prefix, $school_id, $class_open, $class_start, $class_end = null) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            $this->verify_class_exists($class_id);
            $this->is_null_or_empty($title);
            $this->is_null_or_empty($year);
            $this->is_null_or_empty($year_prefix);
            $this->is_null_or_empty($school_id);
            $this->is_null_or_empty($class_open);
            if (!is_bool($class_open)) {
                throw new Exception("ARGUMENT_NOT_BOOL");
            }

            $year_id = $this->get_year_id($year);
            $year_prefix_id = $this->get_year_prefix_id($year_prefix);

            $query = "UPDATE class SET title=:title, description=:description, class_year_id=:year_id,
                        class_year_prefix_id=:year_prefix_id, school_id=:school_id, open=:open, 
                        start_date=:start_date, end_date=:end_date WHERE id = :id";

            if (!DbHandler::get_instance()->query($query, $title, $description, $year_id, $year_prefix_id, $school_id, $class_open, $class_start, $class_end, $class_id)) {
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
            $q_year = "SELECT * FROM class_year";
            $q_prefix = "SELECT * FROM class_year_prefix";

            $this->years = DbHandler::get_instance()->return_query($q_year);
            if (count($this->years) == 0) {
                throw new Exception("CLASS_YEAR_NO_DATA");
            }

            $this->year_prefixes = DbHandler::get_instance()->return_query($q_prefix);
            if (count($this->year_prefixes) == 0) {
                throw new Exception("CLASS_YEAR_PREFIX_NO_DATA");
            }
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_year_prefix_id($year_prefix) {
        $year_prefix_id_array = reset(DbHandler::get_instance()->return_query("SELECT id FROM class_year_prefix WHERE title = :title LIMIT 1", $year_prefix));
        if (empty($year_prefix_id_array)) {
            throw new Exception("OBJECT_IS_EMPTY");
        }
        return $year_prefix_id_array['id'];
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
        if (empty($var)) {
            throw new Exception("OBJECT_IS_EMPTY");
        }

        if (!isset($var)) {
            throw new Exception("OBJECT_DOESNT_EXIST");
        }
    }

    private function verify_user_has_class($user_id) {
        if (!is_int($user_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM user_class WHERE users_id = :id", $user_id);
        if ($count == 0) {
            throw new Exception("USER_HAS_NO_CLASS");
        }
    }

    private function verify_class_exists($class_id) {
        if (!is_int($class_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }

        $count = DbHandler::get_instance()->count_query("SELECT * FROM class WHERE id = :id", $class_id);
        if (!($count == 1)) {
            throw new Exception("CLASS_NOT_FOUND");
        }
    }

    private function verify_class_end_is_greater($class_end, $class_start) {
        $es = strtotime($class_end);
        $ss = strtotime($class_start);
        
        if ($es < $ss) {
            throw new Exception ("CLASS_END_IS_WRONG");
        } 
    }
}
