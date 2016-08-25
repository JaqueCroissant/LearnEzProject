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

    public $global_test_amount;
    public $global_lectures_amount;
    public $global_course_amount;
    public $course_os_distribution;
    public $course_titles;


    //GLOBAL STATS
    public $login_activity = array();
    public $lecture_graph_stats;
    public $test_graph_stats;
    public $school_count = 0;
    public $schools_open = 0;
    public $school_classes_global = 0;
    public $school_type_amount;
    public $account_count = 0;
    public $account_student_teacher_count = 0;
    public $account_max = 0;
    public $accounts_open = 0;
    public $account_type_amount;
    public $account_types = [];
    public $student_total = 0;
    
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
                throw new Exception("INSUFFICIENT_RIGHTS");
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
            $this->class_average = $test_total != 0 || $lect_total != 0 ? round((($this->class_lecture_average * $lect_total) + ($this->class_test_average * $test_total)) / ($lect_total + $test_total), 0) : 0;


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
                throw new Exception("INSUFFICIENT_RIGHTS");
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
            $this->school_average = $test_total != 0 || $lect_total != 0 ? round((($this->school_lecture_average * $lect_total) + ($this->school_test_average * $test_total)) / ($lect_total + $test_total), 0) : 0;
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

    public function get_student_stats($user_id = 0, $days = 7) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if ($user_id == 0) {
                $user_id = $this->_user->id;
            } else {
                $this->verify_user_exist($user_id);
            }
            if (!is_numeric($days)) {
                throw new Exception("INVALID_INPUT_IS_NOT_INT");
            }
            $this->get_student_averages($user_id);
            $this->get_student_totals($user_id);
            $this->get_completion_graph_stats($user_id, $days);
            $this->get_login_activity_for_user($user_id, $days);
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    private function get_login_activity_for_user($user_id, $days = 7) {
        $format = "Y-m-d";
        $login_q = "SELECT count(*) as sum, date(time) as date FROM login_record WHERE users_id = :users_id AND time >= curdate() - interval :days day GROUP BY date ORDER by time";
        $data = DbHandler::get_instance()->return_query($login_q, $user_id, $days);
        $this->login_activity = [];
        $tmp_log = [];
        foreach ($data as $value) {
            $tmp_log[$value['date']] = $value['sum'];
        }
        for ($index = date($format, strtotime('-' . $days . ' days')); $index < date($format, strtotime('+1 days')); $index++) {
            if (array_key_exists($index, $tmp_log)) {
                $this->login_activity[] = (int) $tmp_log[$index];
            } else {
                $this->login_activity[] = 0;
            }
        }
    }

    private function get_completion_graph_stats($user_id, $days) {
        $format = "Y-m-d";
        $lect_q = "SELECT sum(is_complete) as complete, DATE(complete_date) as date FROM `user_course_lecture` WHERE is_complete = 1 and user_id = :user_id AND complete_date >= CURDATE() - INTERVAL :day day group by date ORDER BY date";
        $lect_data = DbHandler::get_instance()->return_query($lect_q, $user_id, $days);
        $test_q = "SELECT sum(is_complete) as complete, DATE(complete_date) as date FROM `user_course_test` WHERE is_complete = 1 and user_id = :user_id AND complete_date >= CURDATE() - INTERVAL :day day group by date ORDER BY date";
        $test_data = DbHandler::get_instance()->return_query($test_q, $user_id, $days);
        $this->lecture_graph_stats = [];
        $this->test_graph_stats = [];
        $temp_lect = [];
        foreach ($lect_data as $value) {
            $temp_lect[$value['date']] = $value['complete'];
        }
        $temp_test = [];
        foreach ($test_data as $value) {
            $temp_test[$value['date']] = $value['complete'];
        }
        $this->test_graph_stats = [];
        for ($index = date($format, strtotime('-' . $days . ' days')); $index < date($format, strtotime('+1 days')); $index++) {
            if (array_key_exists($index, $temp_lect)) {
                $this->lecture_graph_stats[] = (int) $temp_lect[$index];
            } else {
                $this->lecture_graph_stats[] = 0;
            }
            if (array_key_exists($index, $temp_test)) {
                $this->test_graph_stats[] = (int) $temp_test[$index];
            } else {
                $this->test_graph_stats[] = 0;
            }
        }
    }

    private function verify_user_exist($user_id) {
        $count = DbHandler::get_instance()->count_query("SELECT * from users where id = :id", $user_id);
        if ($count == 0) {
            throw new Exception("USER_INVALID_ID");
        }
    }

    private function get_student_averages($user_id) {
        $lectures = [];
        $tests = [];
        $data_array = DbHandler::get_instance()->return_query("SELECT * FROM progress_view WHERE user_id = :user_id", $user_id);

        foreach ($data_array as $value) {
            if ($value['type'] == 1) {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if (!array_key_exists($value['course_id'], $tests)) {
                    $tests[$value['course_id']] = $value['total'] != 0 ? ($progress / $value['total']) * 100 : 0;
                }
            } else {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if (!array_key_exists($value['course_id'], $lectures)) {
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

    public function get_teacher_stats($user_id = 0, $days = 7) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            $this->get_teacher_averages();
            $this->get_teacher_totals();
            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_teacher_averages() {
        $data_array = DbHandler::get_instance()->return_query("SELECT progress_view.* FROM progress_view INNER JOIN user_class ON progress_view.class_id = user_class.class_id WHERE user_class.users_id  = :user_id", $this->_user->id);
        $students = [];
        $courses = [];
        $tests = [];
        $course_progress = 0;
        $course_total = 0;
        $test_progress = 0;
        $test_total = 0;

        foreach ($data_array as $value) {
            if ($value['user_id'] != $this->_user->id) {
                $student_exists = array_key_exists($value['user_id'], $students);
                $course_exists = array_key_exists($value['course_id'], $courses);
                $test_exists = array_key_exists($value['course_id'], $tests);

                if (!$student_exists) {
                    $students[] = $value['user_id'];
                }

                if ($value['type'] == 1) {
                    if (!$test_exists) {
                        $tests[] = $value['course_id'];
                    }

                    if ((!$student_exists && !$test_exists) || ($student_exists && !$test_exists) || (!$student_exists && $test_exists)) {
                        $test_progress += $value['progress'];
                        $test_total += $value['total'];
                    }
                } else {
                    if (!$course_exists) {
                        $courses[] = $value['course_id'];
                    }

                    if ((!$student_exists && !$course_exists) || ($student_exists && !$course_exists) || (!$student_exists && $course_exists)) {
                        $course_progress += $value['progress'];
                        $course_total += $value['total'];
                    }
                }
            }
        }

        $this->teacher_course_average = $course_total != 0 ? round(($course_progress / $course_total) * 100, 0) : 0;
        $this->teacher_test_average = $test_total != 0 ? round(($test_progress / $test_total) * 100, 0) : 0;
    }

    private function get_teacher_totals() {
        
    }

    public function get_top_students($limit = 5, $school = null, $class = null) {
        try {
            $has_school = false;
            $has_class = false;

            if (!is_numeric($limit)) {
                throw new Exception("INVALID_INPUT");
            }

            if (!empty($school)) {
                if (!is_numeric($school)) {
                    throw new Exception("INVALID_INPUT");
                } else {
                    $has_school = true;
                }
            }

            if (!empty($class)) {
                if (!is_numeric($class)) {
                    throw new Exception("INVALID_INPUT");
                } else {
                    $has_class = true;
                }
            }


            $data = array();

            if ($has_school && !$has_class) {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id, image.filename as profile_image FROM users LEFT JOIN image ON image.id = users.image_id INNER JOIN school ON users.school_id = school.id WHERE school.id = :school_id AND users.user_type_id = 4 AND users.open = 1 GROUP BY users.id, users.username, users.firstname, users.surname, users.points ORDER BY users.points DESC LIMIT " . $limit, $school);
            } else if ($has_school && $has_class) {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id, image.filename as profile_image FROM users LEFT JOIN image ON image.id = users.image_id INNER JOIN user_class ON users.id = user_class.users_id INNER JOIN class on user_class.class_id = class.id WHERE class.id = :class_id AND users.user_type_id = 4 AND users.school_id = :school_id AND  users.open = 1 ORDER BY points DESC LIMIT " . $limit, $class, $school);
            } else if (!$has_school && !$has_class) {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id, image.filename as profile_image, school.name, GROUP_CONCAT(class.title SEPARATOR ', ') AS classes FROM users LEFT JOIN image ON image.id = users.image_id INNER JOIN school on users.school_id = school.id INNER JOIN user_class ON users.id = user_class.users_id INNER JOIN class ON class.id = user_class.class_id WHERE users.user_type_id = 4 AND users.open = 1 GROUP BY users.id, users.username, users.firstname, users.surname, users.points ORDER BY users.points DESC LIMIT " . $limit);
            } else if (!$has_school && $has_class) {
                $data = DbHandler::get_instance()->return_query("SELECT users.id, users.username, users.firstname, users.surname, users.points, users.image_id, image.filename as profile_image FROM users LEFT JOIN image ON image.id = users.image_id INNER JOIN user_class ON users.id = user_class.users_id INNER JOIN class on user_class.class_id = class.id WHERE class.id = :class_id AND users.user_type_id = 4 AND  users.open = 1 ORDER BY points DESC LIMIT " . $limit, $class);
            } else {
                throw new Exception("INVALID_INPUT");
            }
            $this->top_students = $data;

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_global_school_stats() {
        try {
            $this->school_classes_global = DbHandler::get_instance()->count_query("SELECT id FROM class");
            $data = DbHandler::get_instance()->return_query("SELECT school.open, school_type.title FROM school INNER JOIN school_type ON school_type.id = school.school_type_id");
            $this->school_count = count($data);

            $types = [];

            foreach ($data as $value) {
                if ($value['open'] == "1") {
                    $this->schools_open++;
                }

                if (!key_exists($value['title'], $types)) {
                    $types[$value['title']] = 1;
                } else {
                    $types[$value['title']] ++;
                }
            }
            $this->school_type_amount = $types;

            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function get_global_account_stats() {
        try {
            $data = [];

            if ($this->_user->user_type_id == "1") {
                $data = DbHandler::get_instance()->return_query("SELECT users.open, translation_user_type.title FROM users INNER JOIN translation_user_type ON translation_user_type.user_type_id = users.user_type_id AND translation_user_type.language_id = :current_lang", TranslationHandler::get_current_language());
            } else {
                $data = DbHandler::get_instance()->return_query("SELECT users.open, users.user_type_id, translation_user_type.title FROM users INNER JOIN translation_user_type ON translation_user_type.user_type_id = users.user_type_id AND translation_user_type.language_id = :current_lang WHERE users.school_id = :school_id AND users.user_type_id > 1", TranslationHandler::get_current_language(), $this->_user->school_id);
                $max_accounts = DbHandler::get_instance()->return_query("SELECT max_students FROM school WHERE id = :school_id", $this->_user->school_id);
                
                $this->account_max = $max_accounts[0]["max_students"];
            }
            

            $this->account_count = count($data);

            $types = [];
            foreach ($data as $value) {
                
                if ($this->_user->user_type_id != "1" && $value['user_type_id'] > 2 && $value['open'] == "1")
                {
                    $this->account_student_teacher_count++;
                }
                
                if ($value['open'] == "1") {
                    $this->accounts_open++;
                }

                if (!key_exists($value['title'], $types)) {
                    $types[$value['title']] = 1;
                } else {
                    $types[$value['title']] ++;
                }
            }
            $this->account_type_amount = $types;

            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function get_login_activity($limit) {
        try {
            if (!is_numeric($limit)) {
                throw new Exception("INVALID_INPUT");
            }
            
            if($this->_user->user_type_id=="1")
            {
                $data = DbHandler::get_instance()->return_query("SELECT login_record.time, translation_user_type.user_type_id, translation_user_type.title FROM login_record INNER JOIN users ON users.id = login_record.users_id INNER JOIN translation_user_type ON users.user_type_id = translation_user_type.user_type_id AND translation_user_type.language_id = :current_lang WHERE time >= NOW() - INTERVAL :limit DAY", TranslationHandler::get_current_language(), $limit);
            }
            else
            {
                $data = DbHandler::get_instance()->return_query("SELECT login_record.time, translation_user_type.user_type_id, translation_user_type.title FROM login_record INNER JOIN users ON users.id = login_record.users_id INNER JOIN translation_user_type ON users.user_type_id = translation_user_type.user_type_id AND translation_user_type.language_id = :current_lang AND users.user_type_id > 1 WHERE time >= NOW() - INTERVAL :limit DAY", TranslationHandler::get_current_language(), $limit);
            }


            $types = [];
            $types['all'] = array();

            foreach($data as $value)
            {
                if(!array_key_exists($value['title'], $types))
                {
                    $types[$value['title']] = array();
                }
                $types[$value['title']][] = $value['time'];
                $types['all'][] = $value['time'];
            }

            foreach($types as $key => $value)
            {
                $types[$key] = $this->convert_to_date_time_array($value);
            }

            $this->login_activity = $types;

            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function get_completion_stats($limit) {
        try {
            if (!is_numeric($limit)) {
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
        catch(Exception $exc)
        {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function convert_to_date_array($data, $field_name) {
        $dates = array();
        foreach ($data as $value) {
            $month = date("m", strtotime($value[$field_name]));
            $day = date("j", strtotime($value[$field_name]));
            $hour = date("G", strtotime($value[$field_name]));

            if (!key_exists($month, $dates)) {
                $dates[$month] = array();
            }

            if (!key_exists($day, $dates[$month])) {
                $dates[$month][$day] = 0;
            }
            $dates[$month][$day] ++;
        }

        return $dates;
    }


    private function convert_to_date_time_array($data)
    {
        $dates = array();
        foreach($data as $value)
        {
            $date = date("Y-m-d", strtotime($value));
            $hour = date("G", strtotime($value));

            if(!key_exists($date, $dates))
            {
                $dates[$date] = array();
            }

            if(!key_exists($hour, $dates[$date]))
            {
                $dates[$date][$hour] = 0;
            }
            $dates[$date][$hour]++;
        }

        return $dates;
    }

    private function sort_and_count($dates) {
        $output = array();

        if (count($dates) > 1) {
            foreach ($dates as $month) {

                asort($month);
                $output += $month;
            }
        } else if (count($dates) > 0) {
            $key = key($dates);
            asort($dates[$key]);

            $output += $dates[$key];
        }

        return $output;
    }

    
    public function get_course_stats()
    {
        try
        {
            if($this->_user->user_type_id=="1")
            {
                $this->super_admin_course_stats();
            }
            else
            {
                $this->local_admin_course_stats();
            }

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    private function super_admin_course_stats()
    {
        $course_data = DbHandler::get_instance()->return_query("SELECT course.os_id, translation_course_os.title FROM course INNER JOIN translation_course_os ON course.os_id = translation_course_os.course_os_id AND translation_course_os.language_id = :current_lang", TranslationHandler::get_current_language());

        $this->global_course_amount = count($course_data);
        $this->global_lectures_amount = DbHandler::get_instance()->count_query("SELECT id FROM course_lecture");
        $this->global_test_amount = DbHandler::get_instance()->count_query("SELECT id FROM course_test");

        $distribution= [];
        foreach($course_data as $value)
        {
            if(!array_key_exists($value['title'], $distribution))
            {
                $distribution[$value['title']] = 0;
            }
            $distribution[$value['title']]++;
        }

        $this->course_os_distribution = $distribution;
    }
    
    private function local_admin_course_stats()
    {
        $lectures_data = DbHandler::get_instance()->return_query("SELECT course_lecture.id, translation_course.title FROM course_lecture INNER JOIN school_course ON course_lecture.course_id = school_course.course_id AND school_course.school_id = :school_id INNER JOIN translation_course ON translation_course.course_id = school_course.course_id AND translation_course.language_id = :current_lang", $this->_user->school_id, TranslationHandler::get_current_language());
        $tests_data = DbHandler::get_instance()->return_query("SELECT course_test.id, translation_course.title FROM course_test INNER JOIN school_course ON course_test.course_id = school_course.course_id AND school_course.school_id = :school_id INNER JOIN translation_course ON translation_course.course_id = school_course.course_id AND translation_course.language_id = :current_lang", $this->_user->school_id, TranslationHandler::get_current_language());
        $lectures_started_data = DbHandler::get_instance()->return_query("SELECT user_course_lecture.user_id, translation_course.title FROM user_course_lecture INNER JOIN users ON user_course_lecture.user_id = users.id AND users.school_id = :school_id AND users.user_type_id > 3 INNER JOIN course_lecture ON course_lecture.id = user_course_lecture.lecture_id INNER JOIN translation_course ON translation_course.course_id = course_lecture.course_id AND translation_course.language_id = :current_lang", $this->_user->school_id, TranslationHandler::get_current_language());
        $tests_started_data = DbHandler::get_instance()->return_query("SELECT user_course_test.user_id, translation_course.title FROM user_course_test INNER JOIN users ON user_course_test.user_id = users.id AND users.school_id = :school_id AND users.user_type_id > 3 INNER JOIN course_test ON course_test.id = user_course_test.test_id INNER JOIN translation_course ON translation_course.course_id = course_test.course_id AND translation_course.language_id = :current_lang", $this->_user->school_id, TranslationHandler::get_current_language());
        
        $this->course_titles = [];
        
        $combined_data = array_merge($lectures_data, $tests_data);
        
        foreach($combined_data as $value)
        {
            if(!array_key_exists($value['title'], $this->course_titles))
            {
                $this->course_titles[$value['title']] = array();
            }
            
            if(!array_key_exists("lectures", $this->course_titles[$value['title']]))
            {
                $this->course_titles[$value['title']]["lectures"] = array();
            }
            
            if(!array_key_exists("tests", $this->course_titles[$value['title']]))
            {
                $this->course_titles[$value['title']]["tests"] = array();
            }
        }

        foreach($lectures_started_data as $value)
        {
            if(!in_array($value['user_id'], $this->course_titles[$value['title']]["lectures"]))
            {
                $this->course_titles[$value['title']]["lectures"][] = $value['user_id'];
            }
        }
        
        foreach($tests_started_data as $value)
        {
            if(!in_array($value['user_id'], $this->course_titles[$value['title']]["tests"]))
            {
                $this->course_titles[$value['title']]["tests"][] = $value['user_id'];
            }
        }

        $this->global_course_amount = count($this->course_titles);
        $this->global_lectures_amount = count($lectures_data);
        $this->global_test_amount = count($tests_data);
    }
    
    public function get_total_students()
    {
        try
        {
            if($this->_user->user_type_id == "2")
            {
                $this->student_total = DbHandler::get_instance()->count_query("SELECT id FROM users WHERE open = 1 AND user_type_id > 3 AND school_id = :school_id", $this->_user->school_id);
            }
            
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
}
