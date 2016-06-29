<?php

class ClassHandler extends Handler {
    public $class;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_class_by_id ($class_id) {
        try {
            $this->verify_class_exists($class_id);
            
            $query =        "SELECT class.title, class.description, class_year.year, translation_class_year_prefix.title 
                            FROM class INNER JOIN class_year ON class.class_year_id = class_year.id
                            INNER JOIN class_year_prefix ON class.class_year_prefix_id = class_year_prefix.id
                            INNER JOIN translation_class_year_prefix ON class_year_prefix_.id = translation_class_year_prefix.class_year_prefix_id
                            WHERE class.id = :id AND translation_class_year_prefix.language_id = :language_id";
            
            $this->class = DbHandler::get_instance()->return_query($query, $class_id, TranslationHandler::get_current_language());
            
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
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
