<?php
    class CourseHandler extends Handler
    {
        public function create_course($os_id = 0, $points = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
            try 
            {
                if (!$this->user_exists()) {
                    throw new exception("USER_NOT_LOGGED_IN");
                }

                if(!RightsHandler::has_user_right("COURSE_CREATE")) {
                    throw new exception("INSUFFICIENT_RIGHTS");
                }
                
                if(empty($os_id) || !is_numeric($os_id) || (!is_numeric($points) && !is_int((int)$points))) {
                    throw new exception("INVALID_COURSE_INPUT");
                }
                
                if(!is_array($titles) || empty($titles) || !is_array($descriptions) || empty($descriptions) || !is_array($language_ids) || empty($language_ids) || count($descriptions) != count($titles)) {
                    throw new exception("INVALID_TRANSLATION_COURSE_INPUT");
                }
                
                $titles = $this->assign_language_id($titles, $language_ids, "title");
                $descriptions = $this->assign_language_id($descriptions, $language_ids, "description");
                $translation_texts = merge_array_recursively($titles, $descriptions);

                if(DbHandler::get_instance()->count_query("SELECT id FROM course_os WHERE id = :os_id", $os_id) < 1) {
                    throw new exception("INVALID_COURSE_INPUT");
                }
                
                if(DbHandler::get_instance()->count_query("SELECT id FROM language WHERE id IN (".generate_in_query($language_ids).")") != count($language_ids)) {
                    throw new exception("INVALID_TRANSLATION_COURSE_INPUT");
                }
                
                DbHandler::get_instance()->query("INSERT INTO course (os_id, points) VALUES (:os_id, :points)", $os_id, $points);
                $last_inserted_id = DbHandler::get_instance()->last_inserted_id();
                
                foreach($translation_texts as $key => $value) {
                    DbHandler::get_instance()->query("INSERT INTO translation_course (course_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $last_inserted_id, $key, $value["title"], $value["description"]);
                }
                return true;
            }
            catch (Exception $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
            return false;
        }
        
        private function assign_language_id($elements = array(), &$language_ids = array(), $key_name = null) {
            if(count($elements) != count($language_ids)) {
                return array();
            }
            
            $array = array();
            for($i = 0; $i < count($language_ids); $i++) {
                if(empty($elements[$i])) {
                    throw new exception("INVALID_INPUT");
                }
                
                if($key_name != null) {
                    $array[$language_ids[$i]][$key_name] = $elements[$i];
                    continue;
                }
                $array[$language_ids[$i]] = $elements[$i];
            }
            return $array;
        }
        
        
        public static function get_os_options(){
            return DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title "
                    . "FROM course_os "
                    . "INNER JOIN translation_course_os "
                    . "ON translation_course_os.course_os_id = course_os.id "
                    . "AND translation_course_os.language_id = :language", TranslationHandler::get_current_language());    
        }
    }


