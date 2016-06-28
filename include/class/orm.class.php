<?php

class ORM {
    protected $data_array;
    
    public function __construct() {
        
        if(func_num_args() != 1) {
            return;
        }
        
        if(!is_array(func_get_args()[0])) {
            return;
        }

        $this->data_array = func_get_args()[0];
        $this->iterate_properties();
        $this->data_array = null;
    }

    private function iterate_properties() {
        foreach(get_object_vars($this) as $key => $value) {
            if(array_key_exists($key, $this->data_array)) {
                $this->$key = $this->data_array[$key];
            }
        }
    }
}
