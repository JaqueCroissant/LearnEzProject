<?php
class PaginationHandler {
    protected $_records_per_page = 0;
    protected $_current_page_number = 0;
    protected $_total_records = 0;
    protected $_total_pages = 0;
    
    protected $_ran_pagination = false;
    protected $_record_array = array();
    public $sliced_record_array = array();
    
    public function run_pagination($array = array(), $current_page_number = 1, $records_per_page = 5) {
        try {
            $this->_ran_pagination = true;

            if(count($array) < 1) {
                return;
            }

            $this->_record_array = $array;
            $this->_records_per_page = $records_per_page;
            $this->_total_records = count($array);
            $this->_total_pages = ceil($this->_total_records/$this->_records_per_page);

            $this->set_current_page($current_page_number);
            $this->set_sliced_array();

            return $this->sliced_record_array;
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    private function set_current_page($current_page_number) {
        if(is_numeric($current_page_number)) {
            if($current_page_number > $this->_total_pages || $current_page_number < 0) {
                $this->_current_page_number = 1;
            } else {
                $this->_current_page_number = $current_page_number;
            }
        } else {
            $this->_current_page_number = 1;
        }
    }
    
    private function set_sliced_array() {
        if($this->_total_records <= $this->_records_per_page) {
            $this->sliced_record_array = $this->_record_array;
        } else {
            $this->sliced_record_array = array_slice($this->_record_array,(($this->_current_page_number-1) < 1 ? 0 : ($this->_current_page_number-1) * $this->_records_per_page), $this->_records_per_page);
        }
    }
    
    public function is_first_page() {
        try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            return $this->_current_page_number <= 1;
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    public function is_last_page() {
        try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            return $this->_current_page_number >= $this->_total_pages;
            
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    public function get_pages_before($amount = 3) {
        try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            $page_array = array();
            for($i = $this->_current_page_number-1; $i > $this->_current_page_number-$amount; $i--) {
                if($i > 0 && $this->_current_page_number > 1) {
                    $page_array[] = $i;
                }
            }
            return $page_array;
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    public function get_pages_after($amount = 3) {
        try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            $page_array = array();
            for($i = $this->_current_page_number+1; $i < $this->_current_page_number+$amount; $i++) {
                if($i <= $this->_total_pages && $this->_current_page_number < $this->_total_pages && $i != 1) {
                    $page_array[] = $i;
                }
            }
            return $page_array;
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    public function get_last_page() {
        try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            if($this->_current_page_number-1 > 0) {
                return $this->_current_page_number-1;
            }
            return 1;
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    public function get_next_page() {
        try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            if($this->_current_page_number+1 <= $this->_total_pages) {
                return $this->_current_page_number+1;
            }
            return $this->_total_pages;
        } catch(Exception $ex) {
            echo $ex;
        }
    }
    
    public function get_final_page() {
       try {
            if(!$this->_ran_pagination) {
                throw new Exception();
            }
            
            return $this->_total_pages;
        } catch(Exception $ex) {
            echo $ex;
        } 
    }
    
    public function build_pagination() {
    }
}

