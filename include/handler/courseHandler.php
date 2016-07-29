<?php
class CourseHandler extends Handler
{
    public $courses = array();
    public $lectures = array();
    public $tests = array();
    public $test;
    public $last_inserted_id;
    private $_all_courses = array();

    public function create_course($os_id = 0, $points = 0, $color = null, $sort_order = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
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
            
            if(empty($color) || !preg_match('/^#[a-f0-9]{6}$/i', $color)) {
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

            DbHandler::get_instance()->query("INSERT INTO course (os_id, points, sort_order, color) VALUES (:os_id, :points, :sort_order, :color)", $os_id, $points, ($sort_order + 1), $color);
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
    
    public function create_lecture($course_id = 0, $points = 0, $difficulty = 0, $sort_order = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
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
            
            DbHandler::get_instance()->query("UPDATE course_lecture SET sort_order = (sort_order + 1) WHERE sort_order > :sort_order", $sort_order);

            DbHandler::get_instance()->query("INSERT INTO course_lecture (course_id, points, sort_order, advanced) VALUES (:course_id, :points, :sort_order, :difficulty)", $course_id, $points, ($sort_order + 1), $difficulty);
            $last_inserted_id = DbHandler::get_instance()->last_inserted_id();

            foreach($translation_texts as $key => $value) {
                DbHandler::get_instance()->query("INSERT INTO translation_course_lecture (course_lecture_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $last_inserted_id, $key, $value["title"], $value["description"]);
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
            
            if((!is_numeric($course_id) && !is_int((int)$course_id))) {
                throw new exception("INVALID_INPUT");
            }
            
            if($course_id == 0) {
                $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, translation_course_lecture.title, translation_course_lecture.description, translation_course.title as course_title FROM course_lecture INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id INNER JOIN translation_course ON translation_course.course_id = course_lecture.course_id WHERE translation_course_lecture.language_id = :language_id", TranslationHandler::get_current_language());
            
            } else {
                $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, translation_course_lecture.title, translation_course_lecture.description, translation_course.title as course_title FROM course_lecture INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id INNER JOIN translation_course ON translation_course.course_id = course_lecture.course_id WHERE translation_course_lecture.language_id = :language_id AND course_lecture.course_id = :course_id", TranslationHandler::get_current_language(), $course_id);
            }
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
            
            if((!is_numeric($course_id) && !is_int((int)$course_id))) {
                throw new exception("INVALID_INPUT");
            }
            
            if($course_id == 0) {
                $data = DbHandler::get_instance()->return_query("SELECT course_test.*, translation_course_test.title, translation_course_test.description, translation_course.title as course_title FROM course_test INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id INNER JOIN translation_course ON translation_course.course_id = course_test.course_id WHERE translation_course_test.language_id = :language_id", TranslationHandler::get_current_language());
            
            } else {
                $data = DbHandler::get_instance()->return_query("SELECT course_test.*, translation_course_test.title, translation_course_test.description, translation_course.title as course_title FROM course_test INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id INNER JOIN translation_course ON translation_course.course_id = course_test.course_id WHERE translation_course_test.language_id = :language_id AND course_test.course_id = :course_id", TranslationHandler::get_current_language(), $course_id);
            }

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
    
    public function get_courses(){
        try{
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if($this->_user->user_type_id == 1) {
                $courses = DbHandler::get_instance()->return_query("SELECT course.id, course.color, course_image.filename as image_filename, translation_course.title, translation_course.description FROM course INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language LEFT JOIN course_image ON course_image.id = course.image_id WHERE course.os_id = :os_id ORDER BY course.sort_order", TranslationHandler::get_current_language(), SettingsHandler::get_settings()->os_id);
            } else {
                $courses = DbHandler::get_instance()->return_query("SELECT course.id, course.color, course_image.filename as image_filename, translation_course.title, translation_course.description FROM course INNER JOIN school_course ON school_course.course_id = course.id AND school_id = :school INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language LEFT JOIN course_image ON course_image.id = course.image_id WHERE course.os_id = :os_id ORDER BY course.sort_order", $this->_user->school_id, TranslationHandler::get_current_language(), SettingsHandler::get_settings()->os_id);
            }
            
            if(empty($courses)) {
                $this->courses = array();
                return true;
            }
            
            $in_array = generate_in_query(array_map(function($o){return $o["id"];}, $courses));
            $course_progress = DbHandler::get_instance()->return_query("SELECT course.id AS course_id, course_test.total_steps AS total, user_course_test.is_complete, user_course_test.progress, 1 AS type FROM course INNER JOIN course_test ON course_test.course_id = course.id LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user WHERE course.id IN(" . $in_array . ") UNION ALL SELECT course.id AS course_id, course_lecture.time_length AS total, user_course_lecture.is_complete, user_course_lecture.progress, 2 AS type FROM course INNER JOIN course_lecture ON course_lecture.course_id = course.id LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user WHERE course.id IN(" . $in_array . ")", $this->_user->id, $this->_user->id);
            $group = array();

            foreach ($course_progress as $course) {
                $group[$course["course_id"]][] = $course;
            }

            $final = array();
            foreach ($courses as $course) {
                if(!array_key_exists($course["id"], $group)){
                    continue;
                }
                $current_progress = 0;
                $amount_of_lectures = 0;
                $amount_of_tests = 0;
                foreach ($group[$course["id"]] as $lecture) {
                    if (isset($lecture["is_complete"]) && $lecture["is_complete"] == "1") {
                        $current_progress += 100;
                    }
                    else if(isset($lecture["progress"])) {
                        $current_progress += $lecture["progress"] / $lecture["total"] * 100;
                    }
                    $amount_of_tests = $lecture["type"] == 1 ? $amount_of_tests + 1 : $amount_of_tests;
                    $amount_of_lectures = $lecture["type"] == 2 ? $amount_of_lectures + 1 : $amount_of_lectures;
                }

                
                $temp = $course;
                $temp["overall_progress"] = count($group[$course["id"]]) < 1 ? 0 : (int)floor($current_progress / count($group[$course["id"]]));
                $temp["amount_of_lectures"]= $amount_of_lectures;
                $temp["amount_of_tests"] = $amount_of_tests;
                array_push($final, new Course($temp));
            }

            $this->courses = $final;
            return true;

        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function load_test($test_id){
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            if (!isset($test_id) || !is_numeric($test_id)) {
                throw new Exception("COURSE_INVALID_ID");                
            }
            if ($this->_user->user_type_id != 1) {
                if(DbHandler::get_instance()->count_query("SELECT course.id FROM course_test INNER JOIN course ON course.id = course_test.course_id INNER JOIN school_course ON school_course.course_id = course.id AND school_course.school_id = :school WHERE course_test.id = :test", $this->_user->school_id, $test_id)){
                    throw new Exception("COURSE_NO_ACCESS");
                }
            }
            
            $test = DbHandler::get_instance()->return_query("SELECT course_test.total_steps, course_test.path, course.color AS course_color, user_course_test.id, user_course_test.progress, user_course_test.is_complete, translation_course.title AS course_title, translation_course_test.title FROM course_test INNER JOIN course ON course.id = course_test.course_id LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id AND translation_course_test.language_id = :language WHERE course_test.id = :test", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $test_id);

            $this->test = new test(reset($test));
            return true;
            
        } catch(Exception $ex){
            $this->error = ErrorHandler::return_error($ex->getMessage());
            echo $this->error->title;
            return false;
        }
        
    }
    
    public function update_progress($type = "", $progress = 0, $is_complete = 0, $table_id = 0, $id = 0){
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            if (!is_int((int)$progress)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            }
            if ($is_complete != 0 && $is_complete != 1) {
                throw new Exception("INVALID_INPUT");
            }
            if (!is_int((int)$table_id) || !is_int((int)$id)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            }
            if ($type == "test") {
                $table = "user_course_test";
            }
            else if ($type == "lecture") {
                $table = "user_course_lecture";
            }
            else {
                throw new Exception("INVALID_INPUT");
            }
            if ($is_complete == 1) {
                $values = "is_complete=1";
            }
            else {
                $values = "progress=" . $progress;
            }
            
            if ($table_id != 0) {
                DbHandler::get_instance()->query("UPDATE " . $table . " SET " . $values . " WHERE id = :id", $table_id);
                return true;
            }
            else if($id != 0) {
                DbHandler::get_instance()->query("INSERT INTO " . $table . " VALUES (:table_id, :user_id, :id, :progress, :is_complete)", null, $this->_user->id, $id, $progress, $is_complete);
                $this->last_inserted_id = DbHandler::get_instance()->last_inserted_id();
                return true;
            }
            else {
                throw new Exception("INVALID_INPUT");
            }
            
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
}


