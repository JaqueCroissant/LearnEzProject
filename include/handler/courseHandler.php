<?php
class CourseHandler extends Handler
{
    public $courses = array();
    public $lectures = array();
    public $tests = array();
    private $_all_courses = array();

    public function create_course($os_id = 0, $points = 0, $sort_order = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($os_id) || !is_numeric($os_id) || (!is_numeric($points) && !is_int((int)$points)) || (!is_numeric($sort_order) && !is_int((int)$sort_order))) {
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
            
            DbHandler::get_instance()->query("UPDATE course SET sort_order = (sort_order + 1) WHERE sort_order > :sort_order", $sort_order);

            DbHandler::get_instance()->query("INSERT INTO course (os_id, points, sort_order) VALUES (:os_id, :points, :sort_order)", $os_id, $points, ($sort_order + 1));
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
    
    public function create_test($course_id = 0, $points = 0, $difficulty = 0, $sort_order = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($course_id) || !is_numeric($course_id) || (!is_numeric($points) && !is_int((int)$points)) || (!is_numeric($sort_order) && !is_int((int)$sort_order)) || (!is_numeric($difficulty) && !is_int((int)$difficulty))) {
                throw new exception("INVALID_INPUT");
            }

            if(!is_array($titles) || empty($titles) || !is_array($descriptions) || empty($descriptions) || !is_array($language_ids) || empty($language_ids) || count($descriptions) != count($titles)) {
                throw new exception("INVALID_TRANSLATION_COURSE_INPUT");
            }

            $titles = $this->assign_language_id($titles, $language_ids, "title");
            $descriptions = $this->assign_language_id($descriptions, $language_ids, "description");
            $translation_texts = merge_array_recursively($titles, $descriptions);

            if(DbHandler::get_instance()->count_query("SELECT id FROM course WHERE id = :course_id", $course_id) < 1) {
                throw new exception("INVALID_INPUT");
            }

            if(DbHandler::get_instance()->count_query("SELECT id FROM language WHERE id IN (".generate_in_query($language_ids).")") != count($language_ids)) {
                throw new exception("INVALID_TRANSLATION_COURSE_INPUT");
            }
            
            DbHandler::get_instance()->query("UPDATE course_test SET sort_order = (sort_order + 1) WHERE sort_order > :sort_order", $sort_order);

            DbHandler::get_instance()->query("INSERT INTO course_test (course_id, points, sort_order, advanced) VALUES (:course_id, :points, :sort_order, :difficulty)", $course_id, $points, ($sort_order + 1), $difficulty);
            $last_inserted_id = DbHandler::get_instance()->last_inserted_id();

            foreach($translation_texts as $key => $value) {
                DbHandler::get_instance()->query("INSERT INTO translation_course_test (course_test_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $last_inserted_id, $key, $value["title"], $value["description"]);
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

    private function set_all_courses() {
        $data = DbHandler::get_instance()->return_query("SELECT course.*, translation_course.title, translation_course.description FROM course INNER JOIN translation_course ON translation_course.course_id = course.id WHERE translation_course.language_id = :language_id ORDER BY course.sort_order", TranslationHandler::get_current_language());
        $array = array();
        foreach($data as $value) {
            $array[] = new Course($value);
        }
        $this->_all_courses = $array;
    }

    public function get_all_courses() {
        if(empty($this->_all_courses)) {
            $this->set_all_courses();
        }
        return $this->_all_courses;
    }
    
    public function get_lectures($course_id = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            if(empty($course_id) || !is_numeric($course_id)) {
                throw new exception("INVALID_INPUT");
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, translation_course_lecture.title, translation_course_lecture.description FROM course_lecture INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id WHERE translation_course_lecture.language_id = :language_id AND course_lecture.course_id = :course_id", TranslationHandler::get_current_language(), $course_id);
            $array = array();
            foreach($data as $value) {
                $array[] = new Lecture($value);
            }
            $this->lectures = $array;
            
            return true;
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_tests($course_id = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            if(empty($course_id) || !is_numeric($course_id)) {
                throw new exception("INVALID_INPUT");
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT course_test.*, translation_course_test.title, translation_course_test.description FROM course_test INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id WHERE translation_course_test.language_id = :language_id AND course_test.course_id = :course_id", TranslationHandler::get_current_language(), $course_id);
            $array = array();
            foreach($data as $value) {
                $array[] = new Test($value);
            }
            $this->tests = $array;
            
            return true;
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_thumbnails() {
        return DbHandler::get_instance()->return_query("SELECT * FROM course_image");
    }
    
    public static function get_os_options(){
        return DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title FROM course_os INNER JOIN translation_course_os ON translation_course_os.course_os_id = course_os.id AND translation_course_os.language_id = :language", TranslationHandler::get_current_language());  
    }
    
    public function get_course_progress(){
        try{
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            $courses = DbHandler::get_instance()->return_query("SELECT course.id AS course_id, "
                    . "translation_course.title "
                    . "FROM course "
                    . "INNER JOIN school_course "
                    . "ON school_course.course_id = course.id "
                    . "AND school_id = :school "
                    . "INNER JOIN translation_course "
                    . "ON translation_course.course_id = course.id "
                    . "AND translation_course.language_id = :language",
                    $this->_user->school_id, TranslationHandler::get_current_language());

            $in_array = generate_in_query(array_map(function($o){return $o["course_id"];}, $courses));

            $course_progress = DbHandler::get_instance()->return_query("SELECT course.id AS course_id, "
                    . "course_test.total_steps AS total, "
                    . "user_course_test.is_complete, "
                    . "user_course_test.progress "
                    . "FROM course "
                    . "INNER JOIN course_test "
                    . "ON course_test.course_id = course.id "
                    . "LEFT JOIN user_course_test "
                    . "ON user_course_test.test_id = course_test.id "
                    . "AND user_course_test.user_id = :user "
                    . "WHERE course.id IN(" . $in_array . ") "
                    . "UNION ALL "
                    . "SELECT course.id AS course_id, "
                    . "course_lecture.time_length AS total, "
                    . "user_course_lecture.is_complete, "
                    . "user_course_lecture.progress "
                    . "FROM course "
                    . "INNER JOIN course_lecture "
                    . "ON course_lecture.course_id = course.id "
                    . "LEFT JOIN user_course_lecture "
                    . "ON user_course_lecture.lecture_id = course_lecture.id "
                    . "AND user_course_lecture.user_id = :user "
                    . "WHERE course.id IN(" . $in_array . ")", $this->_user->id, $this->_user->id);

            $group = array();

            foreach ($course_progress as $course) {
                $group[$course["course_id"]][] = $course;
            }

            $final = array();
            foreach ($courses as $course) {
                $current_progress = 0;
                foreach ($group[$course["course_id"]] as $lecture) {
                    if (isset($lecture["is_complete"]) && $lecture["is_complete"] == "1") {
                        $current_progress += 100;
                    }
                    else if(isset($lecture["progress"])) {
                        $current_progress += $lecture["progress"] / $lecture["total"] * 100;
                    }
                }

                $temp = $course;
                $temp["progress"] = (int)floor($current_progress / count($group[$course["course_id"]]));
                array_push($final, $temp);
            }

            $this->courses = $final;
            return true;

        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_test($test_id){
        //TODO finish this
        return 3;
    }
}


