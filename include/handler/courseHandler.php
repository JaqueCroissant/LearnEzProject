<?php
    class CourseHandler extends Handler
    {
        public function create_course($course = array()) {
            try 
            {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }

                if(!RightsHandler::has_user_right("CREATE_COURSE")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(!is_array($course) || empty($course)) {
                    throw new exception("INVALID_COURSE_INPUT");
                }
                
                $language_ids = array();
                $current_course;
                foreach($course as $value) {
                    if(!is_a($value, "Course")) {
                        throw new exception("INVALID_COURSE_INPUT");
                    }
                    
                    if(empty($value->os_id) || empty($value->title) || empty($value->description) || empty($value->language_id)) {
                        throw new exception("INVALID_COURSE_INPUT");
                    }
                    
                    if(!is_numeric($value->os_id) || !is_numeric($value->language_id) || (!empty($value->points) && !is_numeric($value->points))) {
                        throw new exception("INVALID_COURSE_INPUT");
                    }
                    
                    if(!in_array($value->language_id, $language_ids)) {
                        $language_ids[] = $value->language_id;
                    }
                    
                    $current_course = $value;
                }
                
                if(DbHandler::get_instance()->count_query("SELECT id FROM language WHERE id IN (".generate_in_query($language_ids).")") != count($language_ids)) {
                    throw new exception("INVALID_COURSE_INPUT");
                }
                
                if(DbHandler::get_instance()->count_query("SELECT id FROM course_os WHERE id = :os_id", $current_course->os_id) < 1) {
                    throw new exception("INVALID_COURSE_INPUT");
                }
                
                DbHandler::get_instance()->query("INSERT INTO course (os_id, points) VALUES (:os_id, :points)", $current_course->os_id, $current_course->points);
                $last_inserted_id = DbHandler::get_instance()->last_inserted_id();
                
                foreach($course as $value) {
                    DbHandler::get_instance()->query("INSERT INTO translation_course (course_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $last_inserted_id, $value->language_id, $value->title, $value->description);
                }
                return true;
            }
            catch (Exception $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        
        public static function get_os_options(){
            return DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title "
                    . "FROM course_os "
                    . "INNER JOIN translation_course_os "
                    . "ON translation_course_os.course_os_id = course_os.id "
                    . "AND translation_course_os.language_id = :language", TranslationHandler::get_current_language());    
        }
    }


