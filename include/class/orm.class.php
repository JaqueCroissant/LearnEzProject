<?php

class ORM {
    protected $data_array;
    
    public function __construct($data) {
        if(!is_array($data)) {
            return;
        }

        $this->data_array = $data;
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
