<?php
    class courseHandler extends Handler
    {        
        public static function get_os_options(){
            return DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title "
                    . "FROM course_os "
                    . "INNER JOIN translation_course_os "
                    . "ON translation_course_os.course_os_id = course_os.id "
                    . "AND translation_course_os.language_id = :language", TranslationHandler::get_current_language());    
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
                
                return $final;
         
//                echo "<pre>";
////                var_dump($final);
////                var_dump($courses);
//                var_dump($result);
//                echo "</pre>";
//                return $final;
                
            } catch (Exception $ex) {
                $this->error = ErrorHandler::return_error($ex->getMessage());
                return array();
            }
            
        }
    }

