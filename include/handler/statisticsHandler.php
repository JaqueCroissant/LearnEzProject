<?php

class StatisticsHandler extends Handler {

    public $class_average;
    public $class_test_average;
    public $class_lecture_average;
    private $_school_id;
    private $_class_id;
    private $_account_type_bool;
    public $student_lecture_average;
    public $student_test_average;
    public $student_lecture_complete;
    public $student_test_complete;

    public function __construct() {
        parent::__construct();
    }

    public function get_average_progress_for_class($class_id, $student_and_teacher_bool = false) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            $this->set_class_id($class_id);

            $this->set_account_type_bool($student_and_teacher_bool);

            $base_query = "SELECT * FROM progress_view WHERE user_type_id ";
            if ($this->_account_type_bool) {
                $query = $base_query . "IN (3, 4) AND class_id = :class_id";
            } else {
                $query = $base_query . "= 4 AND class_id = :class_id";
            }
            $data_array = DbHandler::get_instance()->return_query($query, $this->_class_id);
            $progress_array = [];

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

            $lect_progress = 0;
            $lect_total = 0;
            $test_prog = 0;
            $test_total = 0;
            foreach ($progress_array as $key => $value) {
                foreach ($value['courses'] as $nested_key => $nested) {
                    $progress_array[$key][$nested_key]['test_average'] = $nested['test_progress'] != 0 && $nested['test_total'] != 0 ? round($nested['test_progress'] / $nested['test_total'], 2) : 0;
                    $progress_array[$key][$nested_key]['lecture_average'] = $nested['lecture_progress'] != 0 && $nested['lecture_total'] != 0 ? round($nested['lecture_progress'] / $nested['lecture_total'], 2) : 0;
                    $lect_progress += $nested['lecture_progress'];
                    $lect_total += $nested['lecture_total'];
                    $test_prog += $nested['test_progress'];
                    $test_total += $nested['test_total'];
                }
            }
            $this->class_lecture_average = $lect_progress != 0 && $lect_total != 0 ? round($lect_progress / $lect_total, 2) : 0;
            $this->class_test_average = $test_prog != 0 && $test_total != 0 ? round($test_prog / $test_total, 2) : 0;
            $this->class_average = $this->class_lecture_average != 0 && $this->class_test_average != 0 ? round(($this->class_lecture_average + $this->class_test_average) / 2, 2) : 0;
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
            $this->set_school_id($school_id);
            $this->set_account_type_bool($student_and_teacher_bool);
            $base_query = "SELECT * FROM progress_view WHERE user_type_id ";
            if ($this->_account_type_bool) {
                $query = $base_query . "IN (3, 4) AND school_id = :school_id";
            } else {
                $query = $base_query . "= 4 AND school_id = :school_id";
            }
            $data_array = DbHandler::get_instance()->return_query($query, $this->_school_id);
            
            // MANGLER DATABEHANDLING
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

    public function get_average_progress_for_student()
    {
        try
        {
            if (!$this->user_exists())
            {
                throw new exception("USER_NOT_LOGGED_IN");
            }

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
        catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
}
