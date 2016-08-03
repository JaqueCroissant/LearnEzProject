<?php

class Test extends ORM {
    public $id;
    public $user_course_test_id;
    public $course_id;
    public $path;
    public $total_steps;
    
    public $title;
    public $progress;
    public $percent_progress;
    public $is_complete;
    public $course_title;
    public $course_color;
    public $description;
    public $image_filename;
    public $language_id;
    public $sort_order;
    
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
        $this->percent_progress = $this->is_complete == 1 ? 100 : (isset($this->progress) ? round($this->progress / $this->total_steps * 100) : 0);
    }
}

?>