<?php

class HomeworkHandler extends Handler {

    public $available_classes = array();
    public $classes = array();
    public $homework = array();
    public $incomplete_homework = array();
    
    private $homework_data = array();
    private $homework_ids = array();
    private $class_ids = array();
    
    private $date_from;
    private $date_to;

    public function get_user_homework($date_from = null, $date_to = null) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            $this->reset();
            
            if(!empty($date_from) && !empty($date_to)) {
                if($this->validate_date($date_from) && $this->validate_date($date_to)) {
                    $this->date_from = $date_from;
                    $this->date_to = $date_to;
                }
            }
            
            if(!$this->fetch_homework_data()) {
                return true;
            }
            
            $this->iterate_homework_data();
            $this->assign_homework_classes();
            $this->fetch_homework_content();
            $this->assign_class_homework();
            
            return true;
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }

    public function get_available_classes() {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("HOMEWORK_CREATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if ($this->_user->user_type_id == 1) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            $this->available_classes = $this->fetch_attached_classes();
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }

    public function create_homework($description = null, $title = null, $color = null, $classes = array(), $date_expire = null, $lectures = array(), $tests = array()) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("HOMEWORK_CREATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if ($this->_user->user_type_id == 1) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if (empty($title)) {
                throw new exception("MAIL_MUST_FILL_TITLE");
            }
            
            if (empty($color) || !preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                throw new exception("INVALID_INPUT");
            }

            if (empty($classes) || !is_array($classes)) {
                throw new exception("MAIL_MUST_FILL_RECIPIANTS");
            }

            if (((empty($lectures) || !is_array($lectures)) && (empty($tests) || !is_array($tests)))) {
                throw new exception("MUST_PICK_LECTURE_OR_TEST");
            }

            if (!$this->validate_date($date_expire) || strtotime($date_expire) < strtotime(date('Y-m-d'))) {
                throw new exception("INVALID_INPUT_DATE");
            }

            $this->validate_array_input($classes);
            $this->validate_array_input($lectures);
            $this->validate_array_input($tests);


            $classes = $this->assign_receiver_classes($classes);
            $this->assign_homework($lectures, $tests);

            DbHandler::get_instance()->query("INSERT INTO homework (user_id, title, description, color, date_assigned, date_expire) VALUES (:user_id, :title, :description, :color, CURDATE(), :date_expire)", $this->_user->id, $title, $description, $color, $date_expire);
            $last_inserted_id = DbHandler::get_instance()->last_inserted_id();

            foreach ($lectures as $value) {
                DbHandler::get_instance()->query("INSERT INTO homework_lecture (homework_id, lecture_id) VALUES (:homework_id, :lecture_id)", $last_inserted_id, $value);
            }

            foreach ($tests as $value) {
                DbHandler::get_instance()->query("INSERT INTO homework_test (homework_id, test_id) VALUES (:homework_id, :test_id)", $last_inserted_id, $value);
            }

            foreach ($classes as $value) {
                DbHandler::get_instance()->query("INSERT INTO class_homework (homework_id, class_id) VALUES (:homework_id, :class_id)", $last_inserted_id, $value);
            }
            NotificationHandler::create_new_static_user_notification(array_map(function($e) {
                        return $e->id;
                    }, $this->get_class_users($classes)), "HOMEWORK_RECEIVED", array("user" => $this->_user->id, "link_id" => $last_inserted_id));

            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }

    private function validate_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function validate_array_input($array = array()) {
        if (!is_array($array)) {
            throw new exception("MUST_PICK_LECTURE_OR_TEST");
        }

        foreach ($array as $value) {
            if (!is_numeric($value) || !is_int((int) $value)) {
                throw new exception("MUST_PICK_LECTURE_OR_TEST");
            }
        }
    }
    
    private function fetch_homework_data() {
        switch ($this->_user->user_type_id) {
            case 1:
                throw new exception("INSUFFICIENT_RIGHTS");

            case 2:
                if(!empty($this->date_from) && !empty($this->date_to)) {
                    $this->homework_data = DbHandler::get_instance()->return_query("SELECT homework.*, GROUP_CONCAT(class_homework.class_id), users.firstname, users.surname FROM homework INNER JOIN users ON users.id = homework.user_id INNER JOIN class_homework ON class_homework.homework_id = homework.id INNER JOIN class ON class.id = class_homework.class_id WHERE class.school_id = :school_id AND homework.date_expire >= :date_from AND homework.date_expire <= :date_to GROUP BY users.firstname, users.surname, homework.id, homework.user_id, homework.title, homework.description, homework.date_assigned, homework.date_expire, homework.color ORDER BY homework.date_expire DESC", $this->_user->school_id, $this->date_from, $this->date_to);
                } else {
                    $this->homework_data = DbHandler::get_instance()->return_query("SELECT homework.*, GROUP_CONCAT(class_homework.class_id), users.firstname, users.surname  FROM homework INNER JOIN users ON users.id = homework.user_id INNER JOIN class_homework ON class_homework.homework_id = homework.id INNER JOIN class ON class.id = class_homework.class_id WHERE class.school_id = :school_id GROUP BY users.firstname, users.surname, homework.id, homework.user_id, homework.title, homework.description, homework.date_assigned, homework.date_expire, homework.color ORDER BY homework.date_expire DESC", $this->_user->school_id);
                }
                break;
            
            case 3:
            case 4:
                if(!empty($this->date_from) && !empty($this->date_to)) {
                    $this->homework_data = DbHandler::get_instance()->return_query("SELECT homework.*, GROUP_CONCAT(class_homework.class_id), users.firstname, users.surname  FROM homework INNER JOIN users ON users.id = homework.user_id INNER JOIN class_homework ON class_homework.homework_id = homework.id INNER JOIN user_class ON user_class.class_id = class_homework.class_id INNER JOIN class ON class.id = user_class.class_id WHERE user_class.users_id = :user_id AND homework.date_expire >= :date_from AND homework.date_expire <= :date_to GROUP BY users.firstname, users.surname, homework.id, homework.user_id, homework.title, homework.description, homework.date_assigned, homework.date_expire, homework.color ORDER BY homework.date_expire DESC", $this->_user->id, $this->date_from, $this->date_to);
                } else {
                    $this->homework_data = DbHandler::get_instance()->return_query("SELECT homework.*, GROUP_CONCAT(class_homework.class_id), users.firstname, users.surname  FROM homework INNER JOIN users ON users.id = homework.user_id INNER JOIN class_homework ON class_homework.homework_id = homework.id INNER JOIN user_class ON user_class.class_id = class_homework.class_id INNER JOIN class ON class.id = user_class.class_id WHERE user_class.users_id = :user_id GROUP BY users.firstname, users.surname, homework.id, homework.user_id, homework.title, homework.description, homework.date_assigned, homework.date_expire, homework.color ORDER BY homework.date_expire DESC", $this->_user->id);
                }
                break;
        }
        
        if(empty($this->homework_data)) {
            return false;
        }
        return true;
    }
    
    private function iterate_homework_data() {
        $class_ids = array();
        foreach($this->homework_data as $value) {
            $homework = new Homework($value);
            $inner_class_ids = array();
            if(isset($value["GROUP_CONCAT(class_homework.class_id)"]) && !empty($value["GROUP_CONCAT(class_homework.class_id)"])) {

                $classes = explode(",", $value["GROUP_CONCAT(class_homework.class_id)"]);
                foreach($classes as $class_value) {
                    $inner_class_ids[] = $class_value;
                    $class_ids[] = $class_value;
                }
                $homework->class_ids = array_unique($inner_class_ids);
            }

            $this->homework[] = $homework;
            $this->homework_ids[] = $value["id"];
        }
        $this->class_ids = array_values(array_unique($class_ids));
    }
    
    private function assign_homework_classes() {
        $class_data = DbHandler::get_instance()->return_query("SELECT class.id, class.title FROM class WHERE id IN (".  generate_in_query($this->class_ids).")");
        $array = array();
        foreach($class_data as $value) {
            $array[$value["id"]] = $value["title"];
        }
       
        
        foreach($this->homework as $value) {
            foreach($value->class_ids as $class_id) {
                $value->classes[] = new School_Class(array("id" => $class_id, "title" => $array[$class_id]));
            }
        }
    }
    
    
    private function fetch_homework_content() {
        $lecture_data = $this->create_homework_id_array(true, DbHandler::get_instance()->return_query("SELECT course_lecture.*, user_course_lecture.is_complete, translation_course_lecture.title, homework_id FROM homework_lecture INNER JOIN course_lecture ON course_lecture.id = homework_lecture.lecture_id LEFT JOIN user_course_lecture ON user_course_lecture.lecture_id = course_lecture.id AND user_course_lecture.user_id = :user_id INNER JOIN course ON course.id = course_lecture.course_id INNER JOIN translation_course_lecture ON translation_course_lecture.course_lecture_id = course_lecture.id WHERE homework_id IN (".generate_in_query($this->homework_ids).") AND translation_course_lecture.language_id = :language_id AND course.os_id = :os_id", $this->_user->id, TranslationHandler::get_current_language(), SettingsHandler::get_settings()->os_id));
        $test_data = $this->create_homework_id_array(false, DbHandler::get_instance()->return_query("SELECT course_test.*, user_course_test.is_complete, translation_course_test.title, homework_id FROM homework_test INNER JOIN course_test ON course_test.id = homework_test.test_id LEFT JOIN user_course_test ON user_course_test.test_id = course_test.id AND user_course_test.user_id = :user_id INNER JOIN course ON course.id = course_test.course_id INNER JOIN translation_course_test ON translation_course_test.course_test_id = course_test.id WHERE homework_id IN (".generate_in_query($this->homework_ids).") AND translation_course_test.language_id = :language_id AND course.os_id = :os_id", $this->_user->id, TranslationHandler::get_current_language(), SettingsHandler::get_settings()->os_id));
        
        
        foreach($this->homework as $key => $value) {
            $is_complete = true;
            $has_content = false;
            if(array_key_exists($value->id, $lecture_data)) {
                foreach($lecture_data[$value->id] as $lecture_value) {
                    $is_complete = !$lecture_value->is_complete ? false : $is_complete;
                }
                $this->homework[$key]->lectures = $lecture_data[$value->id];
                $has_content = true;
            }
            
            if(array_key_exists($value->id, $test_data)) {
                foreach($test_data[$value->id] as $test_value) {
                    $is_complete = !$test_value->is_complete ? false : $is_complete;
                }
                $this->homework[$key]->tests = $test_data[$value->id];
                $has_content = true;
            }
            
            if(!$has_content) {
                unset($this->homework[$key]);
                continue;
            }
            
            $this->homework[$key]->is_complete = $is_complete;
            
            if(!$is_complete) {
                $this->incomplete_homework[$key] = clone $this->homework[$key];
            }
        } 
    }
    
    private function create_homework_id_array($lecture = true, $data = array()) {
        if(empty($data)) {
            return array();
        }
        
        $array = array();
        foreach($data as $value) {
            $array[$value["homework_id"]][] = $lecture ? new Lecture($value) : new Test($value);
        }
        return $array;
    }

    private function assign_receiver_classes($class_ids) {
        if ($this->_user->user_type_id == 2) {
            $class_ids = DbHandler::get_instance()->return_query("SELECT id FROM class WHERE school_id = :school_id AND id IN (" . generate_in_query($class_ids) . ")", $this->_user->school_id);
        } else {
            $class_ids = DbHandler::get_instance()->return_query("SELECT class.id FROM class INNER JOIN user_class ON user_class.class_id = class.id WHERE user_class.users_id = :user_id AND user_class.class_id IN (" . generate_in_query($class_ids) . ")", $this->_user->id);
        }
        $array = array();
        foreach ($class_ids as $value) {
            $array[] = $value["id"];
        }

        return $array;
    }
    
    private function assign_class_homework() {
        switch ($this->_user->user_type_id) {
            case 2:
            case 3:
                $classes = $this->fetch_attached_classes();
                $class_homework = array();
                
                foreach($this->homework as $homework) {
                    foreach($homework->class_ids as $value) {
                        $class_homework[$value][] = $homework;
                    }
                }
                
                foreach($classes as $class) {
                    $class->homework = $class_homework[$class->id];
                }
                $this->classes = $classes;
                break;
        }
        
        if(empty($this->homework_data)) {
            return false;
        }
        return true;
    }

    private function get_class_users($class_ids) {
        $user_data = DbHandler::get_instance()->return_query("SELECT users.id FROM users INNER JOIN user_class ON user_class.users_id = users.id WHERE user_class.class_id IN (" . generate_in_query($class_ids) . ") AND users.user_type_id = '4'");
        $array = array();
        foreach ($user_data as $value) {
            $array[] = $value["id"];
        }
        $array = array_unique($array);

        $final_array = array();
        foreach ($array as $value) {
            $final_array[] = new User(array("id" => $value));
        }
        return $final_array;
    }

    private function assign_homework(&$lecture_ids, &$test_ids) {
        if (!empty($lecture_ids)) {
            $data = DbHandler::get_instance()->return_query("SELECT course_lecture.id FROM course_lecture INNER JOIN school_course ON school_course.course_id = course_lecture.course_id WHERE school_course.school_id = :school_id AND course_lecture.id IN (" . generate_in_query($lecture_ids) . ")", $this->_user->school_id);
            $array = array();
            foreach ($data as $value) {
                $array[] = $value["id"];
            }
            $lecture_ids = $array;
        }

        if (!empty($test_ids)) {
            $data = DbHandler::get_instance()->return_query("SELECT course_test.id FROM course_test INNER JOIN school_course ON school_course.course_id = course_test.course_id WHERE school_course.school_id = :school_id AND course_test.id IN (" . generate_in_query($test_ids) . ")", $this->_user->school_id);
            $array = array();
            foreach ($data as $value) {
                $array[] = $value["id"];
            }
            $test_ids = $array;
        }
    }

    private function fetch_attached_classes() {
        $classes = array();
        if ($this->_user->user_type_id == 2) {
            $class_data = DbHandler::get_instance()->return_query("SELECT class.id, class.title FROM class WHERE class.open = '1' AND class.end_date >= CURDATE() AND class.school_id = :school_id", $this->_user->school_id);
        } else {
            $class_data = DbHandler::get_instance()->return_query("SELECT class.id, class.title FROM class INNER JOIN user_class ON user_class.class_id = class.id WHERE class.open = '1' AND class.end_date >= CURDATE() AND class.school_id = :school_id AND user_class.users_id = :user_id", $this->_user->school_id, $this->_user->id);
        }

        if (count($class_data) > 0) {
            foreach ($class_data as $class_value) {
                $classes[] = new School_Class($class_value);
            }
        }
        return $classes;
    }
    
    private function reset() {
        $this->available_classes = array();
        $this->homework = array();
        $this->incomplete_homework = array();
        $this->homework_ids = array();
        $this->class_ids = array();

        $this->date_from = null;
        $this->date_to = null;
    }

}
