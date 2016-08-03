<?php
class CourseHandler extends Handler
{
    public $last_inserted_id;
    
    public $courses = array();
    public $lectures = array();
    public $tests = array();
    public $current_element;
    
    public $last_elements = array();
    
    private $_current_element_type;
    private $_current_element_id;
    
    CONST COURSE    = "COURSE";
    CONST LECTURE   = "LECTURE";
    CONST TEST      = "TEST";

    private function set_element_type($element_type = null) {
        if(!is_string($element_type)) {
            throw new exception("INVALID_INPUT");
        }  
        
        switch($element_type) {
            case "course":
                $this->_current_element_type = self::COURSE;
                break;
            
            case "lecture":
                $this->_current_element_type = self::LECTURE;
                break;
            
            case "test":
                $this->_current_element_type = self::TEST;
                break;
            
            default:
                throw new exception("INVALID_INPUT");
        }
        
    }
    
    private function set_element_id($element_id = 0, $allow_empty = false) {
        if(!$allow_empty) {
            if (empty($element_id) || !is_numeric($element_id)) {
                throw new Exception("INVALID_INPUT");
           } 
        } else {
            if(!(is_numeric($element_id) && is_int((int)$element_id))) {
                throw new Exception("INVALID_INPUT");
            }
        }
        $this->_current_element_id = $element_id;
    }
    
    //<editor-fold defaultstate="collapsed" desc="CREATE">
    public function create_course($os_id = 0, $points = 0, $color = null, $sort_order = 0, $thumbnail = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
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
            
            if(empty($thumbnail) || !is_numeric($thumbnail) || (DbHandler::get_instance()->count_query("SELECT id FROM course_image WHERE id = :id", $thumbnail) < 1)) {
                $thumbnail = $this->get_default_thumbnail_id();
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

            DbHandler::get_instance()->query("INSERT INTO course (os_id, points, sort_order, color, image_id) VALUES (:os_id, :points, :sort_order, :color, :image_id)", $os_id, $points, ($sort_order + 1), $color, $thumbnail);
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
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="UPDATE">
    public function assign_school_course($array = array(), $school_id = 0) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            if(!RightsHandler::has_user_right("COURSE_ASSIGN")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            if(empty($school_id) || !is_numeric($school_id) || !is_array($array) || !(DbHandler::get_instance()->count_query("SELECT id FROM school WHERE id = :id", $school_id) > 0)) {
                throw new exception("INVALID_INPUT");
            }
            
            foreach($array as $value) {
                if(empty($value) || !is_numeric($value)) {
                    throw new exception("INVALID_INPUT");
                }
            }
            
            if(count($array) != DbHandler::get_instance()->count_query("SELECT id FROM course WHERE id IN (".generate_in_query($array).")")) {
                throw new exception("INVALID_INPUT");
            }
            
            DbHandler::get_instance()->query("DELETE FROM school_course WHERE school_id = :school_id", $school_id);
            foreach($array as $value) {
                DbHandler::get_instance()->query("INSERT INTO school_course (school_id, course_id) VALUES (:school_id, :course_id)", $school_id, $value);
            }
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    
    public function edit_course($course_id = 0, $os_id = 0, $points = 0, $color = null, $sort_order = 0, $thumbnail = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($course_id) || !is_numeric($course_id) || empty($os_id) || !is_numeric($os_id) || (!is_numeric($points) && !is_int((int)$points)) || (!is_numeric($sort_order) && !is_int((int)$sort_order))) {
                throw new exception("INVALID_INPUT");
            }
            
            if(!(DbHandler::get_instance()->count_query("SELECT id FROM course WHERE id = :id", $course_id) > 0)) {
                throw new exception("INVALID_INPUT");
            }
            
            if(empty($color) || !preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                throw new exception("INVALID_INPUT");
            }

            if(!is_array($titles) || empty($titles) || !is_array($descriptions) || empty($descriptions) || !is_array($language_ids) || empty($language_ids) || count($descriptions) != count($titles)) {
                throw new exception("INVALID_TRANSLATION_COURSE_INPUT");
            }
            
            if(empty($thumbnail) || !is_numeric($thumbnail) || (DbHandler::get_instance()->count_query("SELECT id FROM course_image WHERE id = :id", $thumbnail) < 1)) {
                $thumbnail = $this->get_default_thumbnail_id();
            }

            $titles = $this->assign_language_id($titles, $language_ids, "title");
            $descriptions = $this->assign_language_id($descriptions, $language_ids, "description");
            $translation_texts = merge_array_recursively($titles, $descriptions);

            if(DbHandler::get_instance()->count_query("SELECT id FROM course_os WHERE id = :os_id", $os_id) < 1) {
                throw new exception("INVALID_INPUT");
            }

            if(DbHandler::get_instance()->count_query("SELECT id FROM language WHERE id IN (".generate_in_query($language_ids).")") != count($language_ids)) {
                throw new exception("INVALID_TRANSLATION_COURSE_INPUT");
            }
            
            DbHandler::get_instance()->query("UPDATE course SET sort_order = (sort_order + 1) WHERE sort_order > :sort_order", $sort_order);

            DbHandler::get_instance()->query("UPDATE course SET os_id = :os_id, points = :points, sort_order = :sort_order, color = :color, image_id = :image_id WHERE id = :id", $os_id, $points, ($sort_order + 1), $color, $thumbnail, $course_id);
            
            DbHandler::get_instance()->query("DELETE FROM translation_course WHERE course_id = :course_id", $course_id);
            foreach($translation_texts as $key => $value) {
                DbHandler::get_instance()->query("INSERT INTO translation_course (course_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $course_id, $key, $value["title"], $value["description"]);
            }
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    public function edit_lecture($lecture_id = 0, $course_id = 0, $points = 0, $difficulty = 0, $sort_order = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($lecture_id) || !is_numeric($lecture_id) || empty($course_id) || !is_numeric($course_id) || (!is_numeric($points) && !is_int((int)$points)) || (!is_numeric($sort_order) && !is_int((int)$sort_order)) || (!is_numeric($difficulty) && !is_int((int)$difficulty))) {
                throw new exception("INVALID_INPUT");
            }
            
            if(!(DbHandler::get_instance()->count_query("SELECT id FROM course_lecture WHERE id = :id", $lecture_id) > 0)) {
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

            DbHandler::get_instance()->query("UPDATE course_lecture SET course_id = :course_id, points = :point,  sort_order = :sort_order,  advanced = :advanced WHERE course_lecture.id = :id", $course_id, $points, ($sort_order + 1), $difficulty, $lecture_id);

            DbHandler::get_instance()->query("DELETE FROM translation_course_lecture WHERE course_lecture_id = :lecture_id", $lecture_id);
            foreach($translation_texts as $key => $value) {
                DbHandler::get_instance()->query("INSERT INTO translation_course_lecture (course_lecture_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $lecture_id, $key, $value["title"], $value["description"]);
            }
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    public function edit_test($test_id = 0, $course_id = 0, $points = 0, $difficulty = 0, $sort_order = 0, $titles = array(), $descriptions = array(), $language_ids = array()) {
        try 
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($test_id) || !is_numeric($test_id) || empty($course_id) || !is_numeric($course_id) || (!is_numeric($points) && !is_int((int)$points)) || (!is_numeric($sort_order) && !is_int((int)$sort_order)) || (!is_numeric($difficulty) && !is_int((int)$difficulty))) {
                throw new exception("INVALID_INPUT");
            }
            
            if(!(DbHandler::get_instance()->count_query("SELECT id FROM course_test WHERE id = :id", $test_id) > 0)) {
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

            DbHandler::get_instance()->query("UPDATE course_test SET course_id = :course_id, points = :points, sort_order = :sort_order, advanced = :advanced WHERE course_test.id = :id", $course_id, $points, ($sort_order + 1), $difficulty, $test_id);
            DbHandler::get_instance()->query("DELETE FROM translation_course_test WHERE course_test_id = :test_id", $test_id);
            foreach($translation_texts as $key => $value) {
                DbHandler::get_instance()->query("INSERT INTO translation_course_test (course_test_id, language_id, title, description) VALUES (:course_id, :language_id, :title, :description)", $test_id, $key, $value["title"], $value["description"]);
            }
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
        
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="DELETE">
    public function delete($element_id = 0, $element_type = null) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            
            if(!RightsHandler::has_user_right("COURSE_DELETE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            $this->set_element_id($element_id);
            
            $this->set_element_type($element_type);
            
            $this->delete_element();
            
            return true;
            
        } catch(Exception $ex){
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    private function delete_element() {
        switch(strtolower($this->_current_element_type)) {
            case "course":
                DbHandler::get_instance()->query("DELETE FROM course WHERE id = :id", $this->_current_element_id);
                DbHandler::get_instance()->query("DELETE FROM translation_course WHERE course_id = :id", $this->_current_element_id);
                

                $related_lectures = DbHandler::get_instance()->return_query("SELECT id FROM course_lecture WHERE course_id = :id", $this->_current_element_id);
                $array = array();
                foreach($related_lectures as $value) {
                    $array[] = $value["id"];
                }

                DbHandler::get_instance()->query("DELETE FROM course_lecture WHERE course_id = :id", $this->_current_element_id);
                if(!empty($array)) {
                    DbHandler::get_instance()->query("DELETE FROM user_course_lecture WHERE lecture_id IN (".generate_in_query($array).")");
                    DbHandler::get_instance()->query("DELETE FROM translation_course_lecture WHERE course_lecture_id IN (".generate_in_query($array).")");
                }
                
                $related_tests = DbHandler::get_instance()->return_query("SELECT id FROM course_test WHERE course_id = :id", $this->_current_element_id);
                $array = array();
                foreach($related_tests as $value) {
                    $array[] = $value["id"];
                }

                DbHandler::get_instance()->query("DELETE FROM course_test WHERE course_id = :id", $this->_current_element_id);
                if(!empty($array)) {
                    DbHandler::get_instance()->query("DELETE FROM user_course_test WHERE test_id IN (".generate_in_query($array).")");
                    DbHandler::get_instance()->query("DELETE FROM translation_course_test WHERE course_test_id IN (".generate_in_query($array).")");
                }
                break;

            case "lecture":
                DbHandler::get_instance()->query("DELETE FROM course_lecture WHERE id = :id", $this->_current_element_id);
                DbHandler::get_instance()->query("DELETE FROM user_course_lecture WHERE lecture_id = :id", $this->_current_element_id);
                DbHandler::get_instance()->query("DELETE FROM translation_course_lecture WHERE course_lecture_id = :id", $this->_current_element_id);
                break;

            case "test":
                DbHandler::get_instance()->query("DELETE FROM course_test WHERE id = :id", $this->_current_element_id);
                DbHandler::get_instance()->query("DELETE FROM user_course_test WHERE test_id = :id", $this->_current_element_id);
                DbHandler::get_instance()->query("DELETE FROM translation_course_test WHERE course_test_id = :id", $this->_current_element_id);
                break;
        }
    }
    // </editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="GET">
    private function fetch_element() {
        switch(strtolower($this->_current_element_type)) {
            case "course":
                if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE") && !(DbHandler::get_instance()->count_query("SELECT id FROM school_course WHERE school_id = :school_id AND course_id = :course_id", $this->_user->school_id, $this->_current_element_id) > 0)){
                    throw new Exception("COURSE_NO_ACCESS");
                }
                $data = DbHandler::get_instance()->return_query("SELECT course.*, translation_course.title, translation_course.description FROM course INNER JOIN translation_course ON translation_course.course_id = course.id WHERE translation_course.language_id = :language_id AND course.id = :id LIMIT 1", TranslationHandler::get_current_language(), $this->_current_element_id);
                if(!empty($data)) {
                    $this->current_element = new Course(reset($data));
                }
                break;
            
            case "lecture":
                if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE") && !(DbHandler::get_instance()->count_query("SELECT course.id FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id INNER JOIN school_course ON school_course.course_id = course.id AND school_course.school_id = :school WHERE course_lecture.id = :id", $this->_user->school_id, $this->_current_element_id) > 0)){
                    throw new Exception("COURSE_NO_ACCESS");
                }
                $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, course.color AS course_color, user_course_lecture.id AS user_course_lecture_id, user_course_lecture.progress, user_course_lecture.is_complete, translation_course.title AS course_title,  translation_course_lecture.title, translation_course_lecture.description FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id AND translation_course_lecture.language_id = :language_id WHERE course_lecture.id = :id LIMIT 1", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_current_element_id);
                if(!empty($data)) {
                    $this->current_element = new Lecture(reset($data));
                }
                break;
            
            case "test":
                if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE") && !(DbHandler::get_instance()->count_query("SELECT course.id FROM course_test INNER JOIN course ON course.id = course_test.course_id INNER JOIN school_course ON school_course.course_id = course.id AND school_course.school_id = :school WHERE course_test.id = :id", $this->_user->school_id, $this->_current_element_id) > 0)){
                    throw new Exception("COURSE_NO_ACCESS");
                }
                $data = DbHandler::get_instance()->return_query("SELECT course_test.*, course.color AS course_color, user_course_test.id AS user_course_test_id, user_course_test.progress, user_course_test.is_complete, translation_course.title AS course_title, translation_course_test.title, translation_course_test.description FROM course_test INNER JOIN course ON course.id = course_test.course_id LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id AND translation_course_test.language_id = :language_id WHERE course_test.id = :id LIMIT 1", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_current_element_id);
                if(!empty($data)) {
                    $this->current_element = new Test(reset($data));
                }
                break;
        }
        
        if(empty($this->current_element)) {
            throw new exception("INVALID_INPUT");
        }
    }
    
    private function fetch_all_elements($os_restriction = 0) {
        switch(strtolower($this->_current_element_type)) {
            case "course":
                if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                    if($os_restriction != 0) {
                        $data = DbHandler::get_instance()->return_query("SELECT course.*, translation_course.title, translation_course.description, translation_course_os.title as os_title FROM course INNER JOIN translation_course_os ON translation_course_os.course_os_id = course.os_id INNER JOIN school_course ON school_course.course_id = course.id INNER JOIN translation_course ON translation_course.course_id = course.id WHERE translation_course.language_id = :language_id AND school_course.school_id = :school_id AND translation_course_os.language_id = :language_id AND course.os_id = :os_id ORDER BY course.sort_order", TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_user->school_id, $os_restriction);
                    } else {
                        $data = DbHandler::get_instance()->return_query("SELECT course.*, translation_course.title, translation_course.description, translation_course_os.title as os_title FROM course INNER JOIN translation_course_os ON translation_course_os.course_os_id = course.os_id INNER JOIN school_course ON school_course.course_id = course.id INNER JOIN translation_course ON translation_course.course_id = course.id WHERE translation_course.language_id = :language_id AND school_course.school_id = :school_id AND translation_course_os.language_id = :language_id ORDER BY course.sort_order", TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_user->school_id);
                    }
                } else {
                    if($os_restriction != 0) {
                        $data = DbHandler::get_instance()->return_query("SELECT course.*, translation_course.title, translation_course.description, translation_course_os.title as os_title FROM course INNER JOIN translation_course_os ON translation_course_os.course_os_id = course.os_id INNER JOIN translation_course ON translation_course.course_id = course.id WHERE translation_course.language_id = :language_id AND translation_course_os.language_id = :language_id AND course.os_id = :os_id ORDER BY course.sort_order", TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $os_restriction);
                    } else {
                        $data = DbHandler::get_instance()->return_query("SELECT course.*, translation_course.title, translation_course.description, translation_course_os.title as os_title FROM course INNER JOIN translation_course_os ON translation_course_os.course_os_id = course.os_id INNER JOIN translation_course ON translation_course.course_id = course.id WHERE translation_course.language_id = :language_id AND translation_course_os.language_id = :language_id ORDER BY course.sort_order", TranslationHandler::get_current_language(), TranslationHandler::get_current_language());
                    }
                }
                
                $array = array();
                foreach($data as $value) {
                    $value["amount_of_lectures"] = $count_lectures = DbHandler::get_instance()->count_query("SELECT id FROM course_lecture WHERE course_id = :course_id", $value["id"]);
                    $value["amount_of_tests"] = DbHandler::get_instance()->count_query("SELECT id FROM course_test WHERE course_id = :course_id", $value["id"]);
                    $array[] = new Course($value);
                }
                $this->courses = $array;
                break;
            
            case "lecture":
                if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                    if($this->_current_element_id == 0) {
                        $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, course.color AS course_color, user_course_lecture.id AS user_course_lecture_id, user_course_lecture.progress, user_course_lecture.is_complete, translation_course.title AS course_title,  translation_course_lecture.title, translation_course_lecture.description FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id INNER JOIN school_course ON school_course.course_id = course.id LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id AND translation_course_lecture.language_id = :language_id WHERE school_course.school_id = :school_id ORDER BY course_lecture.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_user->school_id);
                    } else {
                        $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, course.color AS course_color, user_course_lecture.id AS user_course_lecture_id, user_course_lecture.progress, user_course_lecture.is_complete, translation_course.title AS course_title,  translation_course_lecture.title, translation_course_lecture.description FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id INNER JOIN school_course ON school_course.course_id = course.id LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id AND translation_course_lecture.language_id = :language_id WHERE course_lecture.course_id = :course_id AND school_course.school_id = :school_id ORDER BY course_lecture.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_current_element_id, $this->_user->school_id);
                    }
                } else {
                    if($this->_current_element_id == 0) {
                        $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, course.color AS course_color, user_course_lecture.id AS user_course_lecture_id, user_course_lecture.progress, user_course_lecture.is_complete, translation_course.title AS course_title,  translation_course_lecture.title, translation_course_lecture.description FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id  LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id AND translation_course_lecture.language_id = :language_id ORDER BY course_lecture.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language());
                    } else {
                        $data = DbHandler::get_instance()->return_query("SELECT course_lecture.*, course.color AS course_color, user_course_lecture.id AS user_course_lecture_id, user_course_lecture.progress, user_course_lecture.is_complete, translation_course.title AS course_title,  translation_course_lecture.title, translation_course_lecture.description FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id  LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id AND translation_course_lecture.language_id = :language_id WHERE course_lecture.course_id = :course_id ORDER BY course_lecture.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_current_element_id);
                    }
                }
                $array = array();
                foreach($data as $value) {
                    $array[] = new Lecture($value);
                }
                $this->lectures = $array;
                break;
            
            case "test":
                if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                    if($this->_current_element_id == 0) {
                        $data = DbHandler::get_instance()->return_query("SELECT course_test.*, course.color AS course_color, user_course_test.id AS user_course_test_id, user_course_test.progress, user_course_test.is_complete, translation_course.title AS course_title,  translation_course_test.title, translation_course_test.description FROM course_test INNER JOIN course ON course.id = course_test.course_id INNER JOIN school_course ON school_course.course_id = course.id LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id AND translation_course_test.language_id = :language_id WHERE school_course.school_id = :school_id ORDER BY course_test.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_user->school_id);
                    } else {
                        $data = DbHandler::get_instance()->return_query("SELECT course_test.*, course.color AS course_color, user_course_test.id AS user_course_test_id, user_course_test.progress, user_course_test.is_complete, translation_course.title AS course_title,  translation_course_test.title, translation_course_test.description FROM course_test INNER JOIN course ON course.id = course_test.course_id INNER JOIN school_course ON school_course.course_id = course.id LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id AND translation_course_test.language_id = :language_id WHERE course_test.course_id = :course_id AND school_course.school_id = :school_id ORDER BY course_test.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_current_element_id, $this->_user->school_id);
                    }
                } else {
                    if($this->_current_element_id == 0) {
                        $data = DbHandler::get_instance()->return_query("SELECT course_test.*, course.color AS course_color, user_course_test.id AS user_course_test_id, user_course_test.progress, user_course_test.is_complete, translation_course.title AS course_title,  translation_course_test.title, translation_course_test.description FROM course_test INNER JOIN course ON course.id = course_test.course_id  LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id AND translation_course_test.language_id = :language_id ORDER BY course_test.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language());
                    } else {
                        $data = DbHandler::get_instance()->return_query("SELECT course_test.*, course.color AS course_color, user_course_test.id AS user_course_test_id, user_course_test.progress, user_course_test.is_complete, translation_course.title AS course_title,  translation_course_test.title, translation_course_test.description FROM course_test INNER JOIN course ON course.id = course_test.course_id  LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user_id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language_id INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id AND translation_course_test.language_id = :language_id WHERE course_test.course_id = :course_id ORDER BY course_test.sort_order ASC", $this->_user->id, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->_current_element_id);
                    }
                }
                $array = array();
                foreach($data as $value) {
                    $array[] = new Test($value);
                }
                $this->tests = $array;
                break;
        }
    }
    
    private function fetch_last_completed() {
        if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE") && !(DbHandler::get_instance()->count_query("SELECT course.id FROM course_lecture INNER JOIN course ON course.id = course_lecture.course_id INNER JOIN school_course ON school_course.course_id = course.id AND school_course.school_id = :school WHERE course_lecture.id = :id", $this->_user->school_id, $this->_current_element_id) > 0)){
            throw new Exception("COURSE_NO_ACCESS");
        }
        
        $combined_data = [];
        $lecture_data = DbHandler::get_instance()->return_query("SELECT course_lecture.id, user_course_lecture.complete_date, translation_course_lecture.title, 1 AS lecture_type FROM course_lecture INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id INNER JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id WHERE translation_course_lecture.language_id = :language_id AND user_course_lecture.user_id = :user_id AND user_course_lecture.is_complete AND course_lecture.course_id = :course_id ORDER BY user_course_lecture.complete_date DESC LIMIT 5", TranslationHandler::get_current_language(), $this->_user->id, $this->_current_element_id);
        
        if(!empty($lecture_data)) {
            $combined_data = array_merge($lecture_data, $combined_data);
        }
        
        $test_data = DbHandler::get_instance()->return_query("SELECT course_test.id, user_course_test.complete_date, translation_course_test.title, 1 AS test_type FROM course_test INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id INNER JOIN user_course_test ON user_course_test.test_id = course_test.id WHERE translation_course_test.language_id = :language_id AND user_course_test.user_id = :user_id AND user_course_test.is_complete AND course_test.course_id = :course_id ORDER BY user_course_test.complete_date DESC LIMIT 5", TranslationHandler::get_current_language(), $this->_user->id, $this->_current_element_id);
        
        if(!empty($test_data)) {
            $combined_data = array_merge($test_data, $combined_data);
        }
        
        array_sort_by_column($combined_data, "complete_date", SORT_DESC);
        $this->last_elements = $combined_data;
    }

    public function get($element_id = 0, $element_type = null) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            
            $this->set_element_id($element_id);
            
            $this->set_element_type($element_type);
            
            $this->fetch_element();
            
            return true;
            
        } catch(Exception $ex){
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_multiple($element_id = 0, $element_type = null, $os_restriction = 0) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            
            $this->set_element_id($element_id, true);
            
            $this->set_element_type($element_type);
            
            $this->fetch_all_elements($os_restriction);
            
            return true;
            
        } catch(Exception $ex){
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_last_completed($element_id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            
            if (!RightsHandler::has_user_right("COURSE_VIEW")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            
            $this->set_element_id($element_id);
            
            $this->fetch_last_completed();
            
            return true;
            
        } catch(Exception $ex){
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    //</editor-fold>
 
    //<editor-fold defaultstate="collapsed" desc="PROGRESS">
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
                    $temp = $course;
                    $temp["overall_progress"] = 100;
                    $temp["amount_of_lectures"]= 0;
                    $temp["amount_of_tests"] = 0;
                    array_push($final, new Course($temp));
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
                $values = "is_complete=1,complete_date=NOW()";
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
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="THUMBNAIL">
    
    public function upload_thumbnail($file = null) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            if(empty($file) || !is_array($file)) {
                throw new exception("INVALID_INPUT");
            }
            
            if($file["size"] > 1000000) {
                throw new exception("IMAGE_TOO_LARGE_MAX_1_MB");
            }
            
            $file_type = pathinfo($file['name'], PATHINFO_EXTENSION);
            if(!in_array(strtoupper($file_type), array("JPG", "JPEG", "PNG", "GIF"))) {
                throw new exception("IMAGE_MUST_BE_OF_TYPE_JPG_JPEG_PNG_GIF");
            }
            
            $file_location = realpath(__DIR__ . '/../..') . "/assets/images/thumbnails/";
            $file_name = md5(uniqid(mt_rand(), true)) . "." . $file_type;
            if (!move_uploaded_file($file["tmp_name"], $file_location. "uncropped/" . $file_name)) {
                throw new exception("UNKNOWN_ERROR");
            }
            
            $resize = new Resize($file_location. "uncropped/" . $file_name);
            $resize -> resize_image(70, 70, 'auto');
            $resize -> save_image($file_location . "" . $file_name, 100);
            
            DbHandler::get_instance()->query("INSERT INTO course_image (filename) VALUES (:filename)", $file_name);
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    public function set_default_thumbnail($id = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            if(empty($id) || !is_numeric($id)) {
                throw new exception("INVALID_INPUT");
            }
            
            $thumbnail_data = DbHandler::get_instance()->return_query("SELECT * FROM course_image WHERE id = :id LIMIT 1", $id);
            
            if(empty($thumbnail_data)) {
                throw new exception("INVALID_INPUT");
            }
            
            if(reset($thumbnail_data)["default_thumbnail"]) {
                return true;
            }
            
            $old_default_thumbnail = DbHandler::get_instance()->return_query("SELECT * FROM course_image WHERE default_thumbnail = '1' LIMIT 1");
            $old_default_id = !empty($old_default_thumbnail) ? reset($old_default_thumbnail)["id"] : 0;
            DbHandler::get_instance()->query("UPDATE course_image SET default_thumbnail = '0'");
            DbHandler::get_instance()->query("UPDATE course_image SET default_thumbnail = '1' WHERE id = :id", $id);
            DbHandler::get_instance()->query("UPDATE course SET image_id = :id WHERE image_id = :id OR image_id = '0'", $id, $old_default_id);
            return true;
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function delete_thumbnail($id = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            if(empty($id) || !is_numeric($id)) {
                throw new exception("INVALID_INPUT");
            }
            
            $thumbnail_data = DbHandler::get_instance()->return_query("SELECT * FROM course_image WHERE id = :id LIMIT 1", $id);
            
            if(empty($thumbnail_data)) {
                throw new exception("INVALID_INPUT");
            }
            
            if(reset($thumbnail_data)["default_thumbnail"]) {
                throw new exception("CANNOT_DELETE_DEFAULT_THUMBNAIL");
            }
            
            $thumbnail_id = reset($thumbnail_data)["id"];
            $thumbnail_filename = reset($thumbnail_data)["filename"];
            $default_thumbnail = DbHandler::get_instance()->return_query("SELECT * FROM course_image WHERE default_thumbnail = '1'");
            $default_thumbnail_id = !empty($default_thumbnail) ? reset($default_thumbnail)["id"] : 0;
            
            DbHandler::get_instance()->query("UPDATE course SET image_id = :image_id WHERE image_id = :old_image_id", $default_thumbnail_id, $thumbnail_id);
            DbHandler::get_instance()->query("DELETE FROM course_image WHERE id = :id", $thumbnail_id);
            
            $file_location = realpath(__DIR__ . '/../..') . "/assets/images/thumbnails/";
            if(file_exists($file_location . "" . $thumbnail_filename) && file_exists($file_location . "uncropped/" . $thumbnail_filename)) {
                unlink($file_location . "" . $thumbnail_filename);
                unlink($file_location . "uncropped/" . $thumbnail_filename);
            }
            return true;
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_thumbnails() {
        return DbHandler::get_instance()->return_query("SELECT * FROM course_image");
    }
    
    public function get_default_thumbnail_id() {
        return reset(DbHandler::get_instance()->return_query("SELECT id FROM course_image WHERE default_thumbnail = '1' LIMIT 1"))["id"];
    }
    //</editor-fold>
    
    public static function get_os_options(){
        return DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title FROM course_os INNER JOIN translation_course_os ON translation_course_os.course_os_id = course_os.id AND translation_course_os.language_id = :language", TranslationHandler::get_current_language());  
    }
    
}


