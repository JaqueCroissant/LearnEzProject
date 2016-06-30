<?php

class ClassHandler extends Handler {
    public $school_class;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_class_by_id ($class_id) {
        try {
            $this->verify_class_exists($class_id);
            
            $query =        "SELECT class.title, class.description, class_year.year as class_year, translation_class_year_prefix.title as class_year_prefix 
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN class_year_prefix ON class.class_year_prefix_id = class_year_prefix.id
                            INNER JOIN translation_class_year_prefix ON class_year_prefix.id = translation_class_year_prefix.class_year_prefix_id
                            WHERE class.id = :id AND translation_class_year_prefix.language_id = :language_id";
            
            $this->school_class = reset(DbHandler::get_instance()->return_query($query, $class_id, TranslationHandler::get_current_language()));
            
            if (empty($this->school_class)) {
                throw new Exception ("OBJECT_IS_EMPTY");
            }
            return true;
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function create_class($title, $description, $year, $year_prefix) {
        try {
            $this->is_null_or_empty($title);
            $this->is_null_or_empty($year);
            $this->is_null_or_empty($year_prefix);
            
            $year_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM class_year WHERE year = :year LIMIT 1", $year));
            $year_id = $year_id['id'];
            $year_prefix_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM class_year_prefix WHERE title = :title LIMIT 1", $year_prefix));
            $year_prefix_id = $year_prefix_id['id'];
            $query = "INSERT INTO class (title, description, class_year_id, class_year_prefix_id) VALUES (:title, :description, :class_year, :class_year_prefix)";
            
            if (DbHandler::get_instance()->query($query, $title, $description, $year_id, $year_prefix_id)) {
                return true;
            }
            return false;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    private function is_null_or_empty($var) {
        if (empty($var)) {
            throw new Exception ("OBJECT_IS_EMPTY");
        }

        if (!isset($var)) {
            throw new Exception ("OBJECT_DOESNT_EXIST");
        }
    }
    
    private function verify_class_exists ($class_id) {
        if (!is_int($class_id)) {
            throw new Exception ("INVALID_INPUT_IS_NOT_INT");
        }
        
        $count = DbHandler::get_instance()->count_query("SELECT * FROM class WHERE id = :id", $class_id);
        if (!($count == 1)) {
            throw new Exception ("CLASS_NOT_FOUND");
        } 
    }
}
