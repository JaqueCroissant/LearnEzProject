<?php
class HomeworkHandler extends Handler
{
    public $available_students = array();
    public $available_classes = array();
    
    public function get_available($type = "students") {
        try
         {
             if (!$this->user_exists()) {
                 throw new exception("USER_NOT_LOGGED_IN");
             }

             if (!RightsHandler::has_user_right("HOMEWORK_CREATE")) {
                 throw new exception("INSUFFICIENT_RIGHTS");
             }

             if($this->_user->user_type_id == 1) {
                 throw new exception("INSUFFICIENT_RIGHTS");
             }
             
             
             if($type == "students") {
                $this->available_students = $this->fetch_attached_students(); 
             } else {
                $this->available_classes = $this->fetch_attached_classes();
             }
             return true;
         }
         catch (Exception $ex) 
         {
             echo $ex->getMessage();
             $this->error = ErrorHandler::return_error($ex->getMessage());
         }
         return false;
    }
    
    public function create_homework($description = null, $students = array(), $classes = array(), $date_expire = null, $lectures = array(), $tests = array()) {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("HOMEWORK_CREATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if($this->_user->user_type_id == 1) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if(((empty($students) || !is_array($students)) && (empty($classes) || !is_array($classes)))) {
                throw new exception("MAIL_MUST_FILL_RECIPIANTS");
            }

            if(((empty($lectures) || !is_array($lectures)) && (empty($tests) || !is_array($tests)))) {
                throw new exception("MUST_PICK_LECTURE_OR_TEST");
            }

            if(!$this->validate_date($date_expire) || strtotime($date_expire) < strtotime(date('Y-m-d'))) {
                throw new exception("INVALID_INPUT_DATE");
            }

            $this->validate_array_input($students);
            $this->validate_array_input($classes);
            $this->validate_array_input($lectures);
            $this->validate_array_input($tests);


            $students = $this->assign_receiver_students($students);
            if(!empty($classes)) {
                $this->append_receiver_class_students($students, $classes);
            }
            $this->assign_homework($lectures, $tests);
            
            DbHandler::get_instance()->query("INSERT INTO homework (user_id, description, date_assigned, date_expire) VALUES (:user_id, :description, CURDATE(), :date_expire)", $this->_user->id, $description, $date_expire);
            $last_inserted_id = DbHandler::get_instance()->last_inserted_id();
            
            foreach($lectures as $value) {
                DbHandler::get_instance()->query("INSERT INTO homework_lecture (homework_id, lecture_id) VALUES (:homework_id, :lecture_id)", $last_inserted_id, $value);
            }
            
            foreach($tests as $value) {
                DbHandler::get_instance()->query("INSERT INTO homework_test (homework_id, test_id) VALUES (:homework_id, :test_id)", $last_inserted_id, $value);
            }
            
            foreach($students as $value) {
                 DbHandler::get_instance()->query("INSERT INTO user_homework (homework_id, user_id) VALUES (:homework_id, :user_id)", $last_inserted_id, $value);
            }
            return true;
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    private function validate_date($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    private function validate_array_input($array = array()) {
        if(!is_array($array)) {
            throw new exception("MUST_PICK_LECTURE_OR_TEST");
        }
        
        foreach($array as $value) {
            if(!is_numeric($value) || !is_int((int)$value)) {
                throw new exception("MUST_PICK_LECTURE_OR_TEST");
            }
        }
    }
    
    private function append_receiver_class_students(&$student_ids, $class_ids) {
        $class_ids = DbHandler::get_instance()->return_query("SELECT class.id FROM class INNER JOIN user_class ON user_class.class_id = class.id WHERE user_class.users_id = :user_id AND user_class.class_id IN (".generate_in_query($class_ids).")", $this->_user->id);
        
        $array = array();
        foreach($class_ids as $value) {
            $array[] = $value["id"];
        }
        
        $user_data = DbHandler::get_instance()->return_query("SELECT users.id FROM users INNER JOIN user_class ON user_class.users_id = users.id WHERE user_class.class_id IN (".generate_in_query($array).") AND users.user_type_id = '4'");
        
        foreach($user_data as $value) {
            $student_ids[] = $value["id"];
        }
        
        $student_ids = array_unique($student_ids);
    }
    
    private function assign_receiver_students($student_ids) {
        if(empty($student_ids)) {
            return;
        }
        
        $available_class_ids = DbHandler::get_instance()->return_query("SELECT class.id FROM class INNER JOIN user_class ON user_class.class_id = class.id WHERE user_class.users_id = :user_id", $this->_user->id);
        
        $array = array();
        foreach($available_class_ids as $value) {
            $array[] = $value["id"];
        }
        
        $user_data = DbHandler::get_instance()->return_query("SELECT users.id FROM users INNER JOIN user_class ON user_class.users_id = users.id WHERE user_class.class_id IN (".generate_in_query($array).") AND users.id IN (".generate_in_query($student_ids).") AND users.user_type_id = '4'");
        
        $array = array();
        foreach($user_data as $value) {
            $array[] = $value["id"];
        }
        return $array;
    }
    
    private function assign_homework(&$lecture_ids, &$test_ids) {
        if(!empty($lecture_ids)) {
            $data = DbHandler::get_instance()->return_query("SELECT course_lecture.id FROM course_lecture INNER JOIN school_course ON school_course.course_id = course_lecture.course_id WHERE school_course.school_id = :school_id AND course_lecture.id IN (".generate_in_query($lecture_ids).")", $this->_user->school_id);
            $array = array();
            foreach($data as $value) {
                $array[] = $value["id"];
            }
            $lecture_ids = $array;
        }
        
        if(!empty($test_ids)) {
            $data = DbHandler::get_instance()->return_query("SELECT course_test.id FROM course_test INNER JOIN school_course ON school_course.course_id = course_test.course_id WHERE school_course.school_id = :school_id AND course_test.id IN (".generate_in_query($test_ids).")", $this->_user->school_id);
            $array = array();
            foreach($data as $value) {
                $array[] = $value["id"];
            }
            $test_ids = $array;
        }
    }

    private function fetch_attached_classes() {
         $classes = array();
         if($this->_user->user_type_id == 2) {
            $class_data = DbHandler::get_instance()->return_query("SELECT class.id, class.title FROM class WHERE class.open = '1' AND class.end_date >= CURDATE() AND class.school_id = :school_id", $this->_user->school_id);
         } else {
             $class_data = DbHandler::get_instance()->return_query("SELECT class.id, class.title FROM class INNER JOIN user_class ON user_class.class_id = class.id WHERE class.open = '1' AND class.end_date >= CURDATE() AND class.school_id = :school_id AND user_class.users_id = :user_id", $this->_user->school_id, $this->_user->id);
         }

         if(count($class_data) > 0) {
             foreach($class_data as $class_value) {
                 $classes[] = new School_Class($class_value);
             }
         }
         return $classes;
    }

    private function fetch_attached_students() {
        $students = array();
        if($this->_user->user_type_id == 2) {
             $user_data = DbHandler::get_instance()->return_query("SELECT * FROM users WHERE school_id = :school_id AND user_type_id = '4'", $this->_user->school_id);
         } else {
             $array = array();
             $class_ids = DbHandler::get_instance()->return_query("SELECT user_class.class_id FROM user_class WHERE user_class.users_id = :user_id", $this->_user->id);
             foreach($class_ids as $value) {
                 $array[] = $value["class_id"];
             }
             $user_data = DbHandler::get_instance()->return_query("SELECT users.* FROM users INNER JOIN user_class ON user_class.users_id = users.id  INNER JOIN class ON class.id = user_class.class_id WHERE class.open = '1' AND class.end_date >= CURDATE() AND class.school_id = :school_id  AND users.user_type_id = '4' AND user_class.class_id IN (".generate_in_query($array).")", $this->_user->school_id);
         }

         if(count($user_data) > 0) {
             foreach($user_data as $user_value) {
                 $students[] = new User($user_value);
             }
         }
         return $students;
    }
}