<?php

class StatisticsHandler extends Handler {

    public $school_average;
    public $school_test_average;
    public $school_lecture_average;
    public $class_average;
    public $class_test_average;
    public $class_lecture_average;
    public $student_lecture_average;
    public $student_test_average;
    public $student_lectures_complete;
    public $student_tests_complete;
    public $student_lectures_started;
    public $student_tests_started;
    public $student_total_tests;
    public $student_total_lectures;
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

            $base_query = "SELECT course_id, GROUP_CONCAT(total) as total, GROUP_CONCAT(progress) as progress, type from progress_view WHERE user_type_id ";
            if ($this->_account_type_bool) {
                $query = $base_query . "IN (3, 4) AND class_id = :class_id";
            } else {
                $query = $base_query . "= 4 AND class_id = :class_id";
            }
            $query .= ' group by course_id, type'; 
            $data_array = DbHandler::get_instance()->return_query($query, $this->_class_id);
            
            $lecture_avg = [];
            $lecture_total = [];
            $test_avg = [];
            $test_total = [];
            foreach ($data_array as $value) {
                if ($value['type'] == "1") {
                    $test_avg[] = array_sum(explode(',', $value['progress']));
                    $test_total[] = array_sum(explode(',', $value['total']));
                } elseif ($value['type'] == "2") {
                    $lecture_avg[] = array_sum(explode(',', $value['progress']));
                    $lecture_total[] = array_sum(explode(',', $value['total']));
                }
            }
            $this->class_lecture_average = !empty($lecture_avg) && !empty($lecture_total) ? round(array_sum($lecture_avg) * 100 / array_sum($lecture_total), 0)  : 0;
            $this->class_test_average = !empty($test_avg) && !empty($test_total) ? round(array_sum($test_avg) * 100 / array_sum($test_total), 0)  : 0;
            $this->class_average = $this->class_lecture_average != 0 && $this->class_test_average != 0 ? round(($this->class_lecture_average + $this->class_test_average) / 2, 0) : 0;
            
            
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
            $base_query = "SELECT course_id, GROUP_CONCAT(total) as total, GROUP_CONCAT(progress) as progress, type from progress_view WHERE user_type_id ";
            if ($this->_account_type_bool) {
                $query = $base_query . "IN (3, 4) AND school_id = :school_id";
            } else {
                $query = $base_query . "= 4 AND school_id = :school_id";
            }
            $query .= ' group by course_id, type'; 
            $data_array = DbHandler::get_instance()->return_query($query, $this->_school_id);

            $lecture_avg = [];
            $lecture_total = [];
            $test_avg = [];
            $test_total = [];
            foreach ($data_array as $value) {
                if ($value['type'] == "1") {
                    $test_avg[] = array_sum(explode(',', $value['progress']));
                    $test_total[] = array_sum(explode(',', $value['total']));
                } elseif ($value['type'] == "2") {
                    $lecture_avg[] = array_sum(explode(',', $value['progress']));
                    $lecture_total[] = array_sum(explode(',', $value['total']));
                }
            }
            $this->school_lecture_average = !empty($lecture_avg) && !empty($lecture_total) * 100 ? round(array_sum($lecture_avg) / array_sum($lecture_total), 0) : 0;
            $this->school_test_average = !empty($test_avg) && !empty($test_total) ? round(array_sum($test_avg) * 100 / array_sum($test_total), 0) : 0;
            $this->school_average = $this->school_lecture_average != 0 && $this->school_test_average != 0 ? round(($this->school_lecture_average + $this->school_test_average) / 2, 2) : 0;
            
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

    public function get_student_progress($user_id = 0) {
        try {
            if (!$this->user_exists()) {
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

                if (!array_key_exists($value['course_id'], $tests)) {
                    $tests[$value['course_id']] = isset($value['total']) ? ($progress / $value['total']) * 100 : 0;
                }
            } else {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if (!array_key_exists($value['course_id'], $lectures)) {
                    $lectures[$value['course_id']] = isset($value['total']) ? ($progress / $value['total']) * 100 : 0;
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

}
