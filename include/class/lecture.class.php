<?php

class Lecture extends ORM {
    public $id;
    public $user_course_lecture_id;
    public $course_id;
    public $path;
    public $time_length;
    
    public $title;
    public $progress;
    public $percent_progress;
    public $is_complete;
    public $course_title;
    public $course_color;
    public $description;
    public $language_id;
    public $sort_order;
    public $image_filename;
    
    public $points;
    public $advanced;
    
    public function __construct() {
        if(func_num_args() != 1) {
            return;
        }

        if(!is_array(func_get_args()[0])) {
            return;
        }

        parent::__construct(func_get_args()[0]);
        $this->calc_progress();
    }
    
    private function calc_progress(){
        $this->percent_progress = $this->is_complete == 1 ? 100 : (isset($this->progress) ? round($this->progress / $this->time_length * 100) : 0);
    }
}

?>