<?php

class StatisticsHandler extends Handler {

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

    public function __construct() {
        parent::__construct();
    }

    public function get_average_progress_for_class($class_id) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            $progress_array = [];
            $data_array = DbHandler::get_instance()->return_query("SELECT * FROM progress_view WHERE user_type_id = 4 AND class_id = :class_id", $class_id);

            $array_of_course_id = [];
            foreach ($data_array as $value) {
                if (isset($value['course_id'])) {
                    $array_of_course_id[] = $value['course_id'];
                    $progress_array[$value['user_id']]['courses'][$value['course_id']]['course_id'] = $value['course_id'];
                    $progress_array[$value['user_id']]['user_id'] = $value['user_id'];
                    isset($progress_array[$value['user_id']]['courses'][$value['course_id']]["test_progress"]) ? : $progress_array[$value['user_id']]['courses'][$value['course_id']]["test_progress"] = 0;
                    isset($progress_array[$value['user_id']]['courses'][$value['course_id']]['lecture_progress']) ? : $progress_array[$value['user_id']]['courses'][$value['course_id']]['lecture_progress'] = 0;
                    isset($progress_array[$value['user_id']]['courses'][$value['course_id']]['test_total']) ? : $progress_array[$value['user_id']]['courses'][$value['course_id']]['test_total'] = 0;
                    isset($progress_array[$value['user_id']]['courses'][$value['course_id']]['lecture_total']) ? : $progress_array[$value['user_id']]['courses'][$value['course_id']]['lecture_total'] = 0;
                    if ($value['type'] == "1") {
                        $progress_array[$value['user_id']]['courses'][$value['course_id']]["test_progress"] += $value['progress'] != null ? $value['progress'] : 0;
                        $progress_array[$value['user_id']]['courses'][$value['course_id']]["test_total"] += $value['total'] != null ? $value['total'] : 0;
                    } elseif ($value['type'] == "2") {
                        $progress_array[$value['user_id']]['courses'][$value['course_id']]['lecture_progress'] += $value['progress'] != null ? $value['progress'] : 0;
                        $progress_array[$value['user_id']]['courses'][$value['course_id']]["lecture_total"] += $value['total'] != null ? $value['total'] : 0;
                    }
                }
            }

            $count = count($progress_array);
            $this->class_average = 0;
            $this->class_lecture_average = 0;
            $this->class_test_average = 0;
            $lect_progress = 0;
            $lect_total = 0;
            $test_prog = 0;
            $test_total = 0;
            foreach ($progress_array as $key => $value) {
                foreach ($value['courses'] as $nested_key => $nested) {
                    $progress_array[$key][$nested_key]['test_average'] = round($nested['test_progress'] / $nested['test_total'], 2);
                    $progress_array[$key][$nested_key]['lecture_average'] = round($nested['lecture_progress'] / $nested['lecture_total'], 2);
                    $lect_progress += $nested['lecture_progress'];
                    $lect_total += $nested['lecture_total'];
                    $test_prog += $nested['test_progress'];
                    $test_total += $nested['test_total'];
                }
            }
            $this->class_lecture_average = round($lect_progress / $lect_total, 2);
            $this->class_test_average = round($test_prog / $test_total, 2);
            $this->class_average = round(($this->class_lecture_average + $this->class_test_average) / 2, 2);
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_student_progress()
    {
        try
        {
            if (!$this->user_exists())
            {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            $this->get_student_averages();
            $this->get_student_totals();

        }
        catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_student_averages()
    {
        $lectures = [];
        $tests = [];
        $data_array = DbHandler::get_instance()->return_query("SELECT * FROM progress_view WHERE user_id = :user_id", $this->_user->id);

        foreach($data_array as $value)
        {
            if($value['type'] == 1)
            {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if(!array_key_exists($value['course_id'], $tests))
                {
                    $tests[$value['course_id']] = ($progress / $value['total']) * 100;
                }
            }
            else
            {
                $progress = isset($value['progress']) ? $value['progress'] : 0;

                if(!array_key_exists($value['course_id'], $lectures))
                {
                    $lectures[$value['course_id']] = ($progress / $value['total']) * 100;
                }
            }
        }

        $this->student_lecture_average = round(array_sum($lectures) / count($lectures), 0);
        $this->student_test_average = round(array_sum($tests) / count($tests), 0);
    }

    private function get_student_totals()
    {
        $this->student_total_lectures = DbHandler::get_instance()->count_query("SELECT course_lecture.id FROM course_lecture INNER JOIN school_course ON course_lecture.course_id = school_course.course_id WHERE school_course.school_id  = :school_id", $this->_user->school_id);
        $this->student_total_tests = DbHandler::get_instance()->count_query("SELECT course_test.id FROM course_test INNER JOIN school_course ON course_test.course_id = school_course.course_id WHERE school_course.school_id  = :school_id", $this->_user->school_id);
        $lecture_data = DbHandler::get_instance()->return_query("SELECT is_complete FROM user_course_lecture WHERE user_id  = :user_id", $this->_user->id);
        $test_data = DbHandler::get_instance()->return_query("SELECT is_complete FROM user_course_test WHERE user_id  = :user_id", $this->_user->id);

        $this->student_lectures_started = count($lecture_data);
        $this->student_tests_started = count($test_data);
        $this->student_lectures_complete = 0;
        $this->student_tests_complete = 0;

        foreach($lecture_data as $value)
        {
            $this->student_lectures_complete += $value['is_complete'];
        }

        foreach($test_data as $value)
        {
            $this->student_tests_complete += $value['is_complete'];
        }
    }
}
