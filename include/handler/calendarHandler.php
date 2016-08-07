<?php
class CalendarHandler extends Handler
{
    public $current_date;
    public $current_dates = array();
    
    private $_first_date_day;
    private $_last_date_day;
    
    public function __construct($selected_date = null) {
        parent::__construct();
        $this->current_date = new Calendar_Date($this->generate_selected_date($selected_date), $this->generate_selected_date($selected_date));
        $this->generate_dates();
    }

    private function validate_date($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    private function generate_selected_date($offset = 0) {
        if(!is_numeric($offset)) {
            return date('Y-m-d');
        }
        return date('Y-m-d', strtotime(date('Y-m-d') .' ' . $offset . ' months'));
    }
    
    private function generate_dates() {
        try {
            
            $dates = array();
            
            for($i = 0; $i < date("t", strtotime($this->current_date->full_date)); $i++) {
                $fist_month_day = date('Y', strtotime($this->current_date->full_date)) . "-" . date('m', strtotime($this->current_date->full_date)) . "-01";
                $current_date = date('Y-m-d', strtotime($fist_month_day .' ' . $i . ' days'));
                $dates[] = new Calendar_Date($current_date, $this->current_date->full_date);
            }
            
            $this->_first_date_day = date('N', strtotime($dates[0]->full_date));
            $this->_last_date_day = date('N', strtotime(array_pop((array_slice($dates, -1)))->full_date));
            
            $this->generate_pre_dates();
            $this->current_dates = array_merge($this->current_dates, $dates);
            $this->generate_post_dates();
        }
        catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
    }
    
    private function generate_pre_dates() {
        if($this->_first_date_day == 1) {
            return;
        }
        
        $last_month = date('Y-m-t', strtotime($this->current_date->full_date .' -1 months'));
        $dates = array();
        
        for($i = 0; $i < $this->_first_date_day-1; $i++) {
            $current_date = date('Y-m-d', strtotime($last_month .' -' . $i . ' days'));
            $dates[] = new Calendar_Date($current_date, $this->current_date->full_date);
        }
        
        $this->current_dates = array_merge($this->current_dates, array_reverse($dates));
    }
    
     private function generate_post_dates() {
        if($this->_last_date_day == 7) {
            return;
        }
        
        $next_month = date('Y-m-01', strtotime($this->current_date->full_date .' +1 months'));
        $dates = array();
        
        for($i = 0; $i < 7-$this->_last_date_day; $i++) {
            $current_date = date('Y-m-d', strtotime($next_month .' +' . $i . ' days'));
            $dates[] = new Calendar_Date($current_date, $this->current_date->full_date);
        }
        
        $this->current_dates = array_merge($this->current_dates, $dates);
    }

    public function generate_current_date_string() {
        return TranslationHandler::get_static_text("WEEK_DAY_" . strtoupper(date('l', strtotime(date('Y-m-d'))))) . " - " . (substr(date('d'), 0, 1) == 0 ? substr(date('d'), 1) : date('d')) . ". " . TranslationHandler::get_static_text(strtoupper(month_num_to_string(date('m'))));
    }
    
    
}