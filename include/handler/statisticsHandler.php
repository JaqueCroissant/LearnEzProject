<?php

class StatisticsHandler extends Handler {

    // SCHOOL STATS
    public $school_average;
    public $school_test_average;
    public $school_lecture_average;

    // CLASS STATS
    public $class_average;
    public $class_test_average;
    public $class_lecture_average;

    //STUDENT STATS
    public $student_lecture_average;
    public $student_test_average;
    public $student_lectures_complete;
    public $student_tests_complete;
    public $student_lectures_started;
    public $student_tests_started;
    public $student_total_tests;
    public $student_total_lectures;

    //TEACHER STATS
    public $teacher_course_average;
    public $teacher_test_average;
    public $teacher_lectures_complete;
    public $teacher_tests_complete;
    public $teacher_lectures_started;
    public $teacher_tests_started;
    public $teacher_total_tests;
    public $teacher_total_lectures;

    //TOP STUDENTS
    public $top_students;

    //LECTURE & TEST
    public $global_tests_complete;
    public $global_lectures_complete;

    //GLOBAL STATS
    public $login_activity = array();
    public $school_count = 0;
    public $schools_open = 0;
    public $school_classes_global = 0;
    public $school_type_amount;
    public $account_count = 0;
    public $accounts_open = 0;
    public $account_type_amount;

    private $_school_id;
    private $_class_id;
    private $_account_type_bool;

    public function __construct() {
        parent::__construct();
    }

    public function get_average_progress_for_class($class_id, $student_and_teacher_bool = false) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("CLASS_STATISTICS")) {
                throw new Exception ("INSUFFICIENT_RIGHTS");
            }
            $this->set_class_id($class_id);

            $this->set_account_type_bool($student_and_teacher_bool);

            $base_query = "SELECT course_id, GROUP_CONCAT(progress / total) as progress, count(total) as total, type from progress_view WHERE user_type_id ";
            if ($this->_account_type_bool) {
                $query = $base_query . "IN (3, 4) AND class_id = :class_id";
            } else {
                $query = $base_query . "= 4 AND class_id = :class_id";
            }
            $query .= ' group by course_id, type'; 
            $data_array = DbHandler::get_instance()->return_query($query, $this->_class_id);
            $lecture_avg = [];
            $lect_total = 0;
            $test_avg = [];
            $test_total = 0;
            foreach ($data_array as $value) {
                if ($value['type'] == "1") {
                    $test = explode(',', $value['progress']);
                    $test_avg[] = $value['total'] != 0 ? array_sum($test) / $value['total'] : 0;
                    $test_total += $value['total'];
                } elseif ($value['type'] == "2") {
                    $lect = explode(',', $value['progress']);
                    $lecture_avg[] = $value['total'] != 0 ? array_sum($lect) / $value['total'] : 0;
                    $lect_total += $value['total'];
                }
            }
            $this->class_lecture_average = array_sum($lecture_avg) != 0 ? round(array_sum($lecture_avg) * 100 / count($lecture_avg), 0) : 0;
            $this->class_test_average = array_sum($test_avg) != 0 ? round(array_sum($test_avg) * 100 / count($test_avg), 0) : 0;
            $this->class_average = $test_total != 0 || $lect_total != 0 ? round((($this->class_lecture_average * $lect_total) + ($this->class_test_average * $test_total)) / ($lect_total + $test_total),0) : 0;
            
            
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_average_for_school($school_id, $student_and_teacher_bool = false) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!RightsHandler::has_user_right("SCHOOL_STATISTICS")) {
                throw new Exception ("INSUFFICIENT_RIGHTS");
            }
            $this->set_school_id($school_id);
            $this->set_account_type_bool($student_and_teacher_bool);
            $base_query = "SELECT course_id, GROUP_CONCAT(progress / total) as progress, count(total) as total, type from progress_view WHERE user_type_id ";
            if ($this->_account_type_bool) {
                $query = $base_query . "IN (3, 4) AND school_id = :school_id";
            } else {
                $query = $base_query . "= 4 AND school_id = :school_id";
            }
            $query .= ' group by course_id, type'; 
            $data_array = DbHandler::get_instance()->return_query($query, $this->_school_id);
            $lecture_avg = [];
            $lect_total = 0;
            $test_avg = [];
            $test_total = 0;
            foreach ($data_array as $value) {
                if ($value['type'] == "1") {
                    $test = explode(',', $value['progress']);
                    $test_avg[] = $value['total'] != 0 ? array_sum($test) / $value['total'] : 0;
                    $test_total += $value['total'];
                } elseif ($value['type'] == "2") {
                    $lect = explode(',', $value['progress']);
                    $lecture_avg[] = $value['total'] != 0 ? array_sum($lect) / $value['total'] : 0;
                    $lect_total += $value['total'];
                }
            }
            $this->school_lecture_average = array_sum($lecture_avg) != 0 ? round(array_sum($lecture_avg) * 100 / count($lecture_avg), 0) : 0;
            $this->school_test_average = array_sum($test_avg) != 0 ? round(array_sum($test_avg) * 100 / count($test_avg), 0) : 0;
            $this->school_average = $test_total != 0 || $lect_total != 0 ? round((($this->school_lecture_average * $lect_total) + ($this->school_test_average * $test_total)) / ($lect_total + $test_total),0) : 0;
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function set_account_type_bool($bool) {
        if (!is_bool($bool)) {
            throw new Exception("ARGUMENT_NOT_BOOL");
        }
        $this->_account_type_bool = $bool;
    }

    private function set_school_id($school_id) {
        if (!is_numeric($school_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
        $count = DbHandler::get_instance()->count_query("SELECT id FROM school WHERE id = :id", $school_id);
        if ($count == 0) {
            throw new Exception("SCHOOL_NOT_FOUND");
        } else {
            $this->_school_id = $school_id;
        }
    }

    private function set_class_id($class_id) {
        if (!is_numeric($class_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
        $count = DbHandler::get_instance()->count_query("SELECT id FROM class WHERE id = :id", $class_id);
        if ($count == 0) {
            throw new Exception("SCHOOL_NOT_FOUND");
        } else {
            $this->_class_id = $class_id;
        }
    }

    public function get_student_stats($user_id = 0)
    {
        try
        {
            if (!$this->user_exists())
            {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if ($user_id == 0) {
                $user_id = $this->_user->id;
            } else {
                $this->verify_user_exist($user_id);
            }
            $this->get_student_averages($user_id);
            $this->get_student_totals($user_id);
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    private function verify_user_exist($user_id) {
        $count = DbHandler::get_instance()->count_query("SELECT * from users where id = :id", $user_id);
        if ($count == 0) {
            throw new Exception ("USER_INVALID_ID");
        }
    }

    private function get_student_averages($user_id) {
        $lectures = [];
        $tests = [];
        $data_array = DbHandler::get_instance()->return_query("SELECT * FROM progress_view WHERE user_id = :user_id", $user_id);
        
        foreach ($data_array as $value) {
            if ($value['type'] == 1) {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if(!array_key_exists($value['course_id'], $tests))
                {
                    $tests[$value['course_id']] = $value['total'] != 0 ? ($progress / $value['total']) * 100 : 0;
                }
            } else {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if(!array_key_exists($value['course_id'], $lectures))
                {
                    $lectures[$value['course_id']] = $value['total'] != 0 ? ($progress / $value['total']) * 100 : 0;
                }
            }
        }

        $this->student_lecture_average = round(array_sum($lectures) / count($lectures), 0);
        $this->student_test_average = round(array_sum($tests) / count($tests), 0);
    }

    private function get_student_totals($user_id) {
        $school_id = DbHandler::get_instance()->return_query("SELECT school_id FROM users where id = :id", $user_id)[0]['school_id'];
        $this->student_total_lectures = DbHandler::get_instance()->count_query("SELECT course_lecture.id FROM course_lecture INNER JOIN school_course ON course_lecture.course_id = school_course.course_id WHERE school_course.school_id  = :school_id", $school_id);
        $this->student_total_tests = DbHandler::get_instance()->count_query("SELECT course_test.id FROM course_test INNER JOIN school_course ON course_test.course_id = school_course.course_id WHERE school_course.school_id  = :school_id", $school_id);
        $lecture_data = DbHandler::get_instance()->return_query("SELECT is_complete FROM user_course_lecture WHERE user_id  = :user_id", $user_id);
        $test_data = DbHandler::get_instance()->return_query("SELECT is_complete FROM user_course_test WHERE user_id  = :user_id", $user_id);

        $this->student_lectures_started = count($lecture_data);
        $this->student_tests_started = count($test_data);
        $this->student_lectures_complete = 0;
        $this->student_tests_complete = 0;

        foreach ($lecture_data as $value) {
            $this->student_lectures_complete += $value['is_complete'];
        }

        foreach ($test_data as $value) {
            $this->student_tests_complete += $value['is_complete'];
        }
    }

    public function get_teacher_stats()
    {
        try
        {
            if (!$this->user_exists())
            {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            $this->get_teacher_averages();
            $this->get_teacher_totals();
            return true;
        }
        catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }

    }

    private function get_teacher_averages()
    {
        $data_array = DbHandler::get_instance()->return_query("SELECT progress_view.* FROM progress_view INNER JOIN user_class ON progress_view.class_id = user_class.class_id WHERE user_class.users_id  = :user_id", $this->_user->id);
        $students = [];
        $courses = [];
        $tests = [];
        $course_progress = 0;
        $course_total = 0;
        $test_progress = 0;
        $test_total = 0;

        foreach($data_array as $value)
        {
            if($value['user_id'] != $this->_user->id)
            {
                $student_exists = array_key_exists($value['user_id'], $students);
                $course_exists = array_key_exists($value['course_id'], $courses);
                $test_exists = array_key_exists($value['course_id'], $tests);

                if(!$student_exists)
                {
                    $students[] = $value['user_id'];
                }

                if($value['type'] == 1)
                {
                    if(!$test_exists)
                    {
                        $tests[] = $value['course_id'];
                    }

                    if((!$student_exists && !$test_exists) || ($student_exists && !$test_exists) || (!$student_exists && $test_exists))
                    {
                        $test_progress += $value['progress'];
                        $test_total += $value['total'];
                    }
                }
                else
                {
                    if(!$course_exists)
                    {
                        $courses[] = $value['course_id'];
                    }

                    if((!$student_exists && !$course_exists) || ($student_exists && !$course_exists) || (!$student_exists && $course_exists))
                    {
                        $course_progress += $value['progress'];
                        $course_total += $value['total'];
                    }
                }

            }
        }

        $this->teacher_course_average = $course_total != 0 ? round(($course_progress / $course_total) * 100, 0) : 0;
        $this->teacher_test_average = $test_total != 0 ? round(($test_progress / $test_total) * 100, 0) : 0;
    }

    private function get_teacher_totals()
    {

    }

    public function get_top_students($limit = 5, $school = null, $class = null)
    {
        try
        {
            $has_school = false;
            $has_class = false;

            if(!is_numeric($limit))
            {
                throw new Exception("INVALID_INPUT");
            }

            if(!empty($school))
            {
                if(!is_numeric($school))
                {
                    throw new Exception("INVALID_INPUT");
                }
                else
                {
                    $has_school = true;
                }
            }

            if(!empty($class))
            {
                if(!is_numeric($class))
                {
                    throw new Exception("INVALID_INPUT");
                }
                else
                {
                    $has_class = true;
                }
            }


            $data = array();

            if($has_school && !$has_class)
            {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id FROM users INNER JOIN school ON users.school_id = school.id WHERE school.id = :school_id AND users.user_type_id = 4 GROUP BY users.id, users.username, users.firstname, users.surname, users.points ORDER BY users.points DESC LIMIT :limit", $school, $limit);
            }
            else if($has_school && $has_class)
            {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id FROM users INNER JOIN user_class ON users.id = user_class.users_id INNER JOIN class on user_class.class_id = class.id WHERE class.id = :class_id AND users.user_type_id = 4 AND users.school_id = :school_id ORDER BY points DESC LIMIT :limit", $class, $school, $limit);
            }
            else if(!$has_school && !$has_class)
            {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id, school.name, GROUP_CONCAT(class.title SEPARATOR ', ') AS classes FROM users INNER JOIN school on users.school_id = school.id INNER JOIN user_class ON users.id = user_class.users_id INNER JOIN class ON class.id = user_class.class_id WHERE users.user_type_id = 4 GROUP BY users.id, users.username, users.firstname, users.surname, users.points ORDER BY users.points DESC LIMIT :limit", $limit);
            } else if (!$has_school && $has_class) {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id FROM users INNER JOIN user_class ON users.id = user_class.users_id INNER JOIN class on user_class.class_id = class.id WHERE class.id = :class_id AND users.user_type_id = 4 ORDER BY points DESC LIMIT :limit", $class, $limit);
            }
            else
            {
                throw new Exception("INVALID_INPUT");
            }
            $this->top_students = $data;

            return true;
        }
        catch(Exception $exc)
        {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function get_global_school_stats()
    {
        try
        {
            $this->school_classes_global = DbHandler::get_instance()->count_query("SELECT id FROM class");
            $data = DbHandler::get_instance()->return_query("SELECT school.open, school_type.title FROM school INNER JOIN school_type ON school_type.id = school.school_type_id");
            $this->school_count = count($data);
            
            $types = [];
            
            foreach($data as $value)
            {
                if($value['open']=="1")
                {
                    $this->schools_open++;
                }
                
                if(!key_exists($value['title'], $types))
                {
                    $types[$value['title']] = 1;
                }
                else
                {
                    $types[$value['title']]++;
                }                
            }
            $this->school_type_amount = $types;

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_global_account_stats()
    {
        try
        {
            $data = [];
            
            if($this->_user->user_type_id == "1")
            {
                $data = DbHandler::get_instance()->return_query("SELECT users.open, translation_user_type.title FROM users INNER JOIN translation_user_type ON translation_user_type.user_type_id = users.user_type_id AND translation_user_type.language_id = :current_lang", TranslationHandler::get_current_language());
            }
            else
            {
                $data = DbHandler::get_instance()->return_query("SELECT user_type_id, open FROM users WHERE user_type_id > 1 AND school_id = :school_id", $this->_user->school_id);
            }
            
            $this->account_count = count($data);
            
            $types = [];       
            foreach($data as $value)
            {
                if($value['open']=="1")
                {
                    $this->accounts_open++;
                }
                
                if(!key_exists($value['title'], $types))
                {
                    $types[$value['title']] = 1;
                }
                else
                {
                    $types[$value['title']]++;
                }                
            }
            $this->account_type_amount = $types;

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function get_login_activity($limit)
    {
        try
        {
            if(!is_numeric($limit))
            {
                throw new Exception("INVALID_INPUT");
            }

            $data = DbHandler::get_instance()->return_query("SELECT time FROM login_record WHERE time >= NOW() - INTERVAL :limit DAY", $limit);
            $dates = $this->convert_to_date_time_array($data, "time");
            $sorted_dates = $this->sort_and_count($dates);
            $this->login_activity = $sorted_dates;

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }

    }

    public function get_completion_stats($limit)
    {
        try
        {
            if(!is_numeric($limit))
            {
                throw new Exception("INVALID_INPUT");
            }

            $lecture_data = DbHandler::get_instance()->return_query("SELECT complete_date FROM user_course_lecture WHERE complete_date >= CURDATE() - INTERVAL :limit DAY", $limit);
            $test_data = DbHandler::get_instance()->return_query("SELECT complete_date FROM user_course_test WHERE complete_date >= CURDATE() - INTERVAL :limit DAY", $limit);

            $lecture_dates = $this->convert_to_date_array($lecture_data, "complete_date");
            $test_dates = $this->convert_to_date_array($test_data, "complete_date");

            $this->global_lectures_complete = $this->sort_and_count($lecture_dates);
            $this->global_tests_complete = $this->sort_and_count($test_dates);

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function convert_to_date_array($data, $field_name)
    {
        $dates = array();
        foreach($data as $value)
        {
            $month = date("m", strtotime($value[$field_name]));
            $day = date("j", strtotime($value[$field_name]));
            $hour = date("G", strtotime($value[$field_name]));

            if(!key_exists($month, $dates))
            {
                $dates[$month] = array();
            }

            if(!key_exists($day, $dates[$month]))
            {
                $dates[$month][$day] = 0;
            }
            $dates[$month][$day]++;
        }

        return $dates;
    }

    private function convert_to_date_time_array($data, $field_name)
    {
        $dates = array();
        foreach($data as $value)
        {
            $month = date("m", strtotime($value[$field_name]));
            $day = date("j", strtotime($value[$field_name]));
            $hour = date("G", strtotime($value[$field_name]));

            if(!key_exists($month, $dates))
            {
                $dates[$month] = array();
            }

            if(!key_exists($day, $dates[$month]))
            {
                $dates[$month][$day] = array();
            }

            if(!key_exists($hour, $dates[$month][$day]))
            {
                $dates[$month][$day][$hour] = 0;
            }
            $dates[$month][$day][$hour]++;
        }

        return $dates;
    }

    private function sort_and_count($dates)
    {
        $output = array();

        if(count($dates) > 1)
        {
            foreach($dates as $month)
            {

                asort($month);
                $output += $month;
            }
        }
        else if(count($dates) > 0)
        {
            $key = key($dates);
            asort($dates[$key]);

            $output += $dates[$key];
        }

        return $output;
    }



}
