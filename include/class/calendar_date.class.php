<?php
class Calendar_Date {
    public $full_date;
    public $day;
    public $month;
    public $year;
    
    public $day_title;
    public $month_title;
    
    public $in_current_month;
    public $is_today;
    
    public function __construct($date = null, $current_month = null) {
        
        $date = $this->validate_date($date) ? $date : date('Y-m-d');
        $current_month = $this->validate_date($current_month) ? $current_month : date('Y-m-d');
        
        $this->full_date = $date;
        $this->in_current_month = date('m', strtotime($this->full_date)) == date('m', strtotime($current_month));
        $this->is_today = date('Y-m-d') == date('Y-m-d', strtotime($date));
        
        $this->day = substr(date('d', strtotime($this->full_date)), 0, 1) == 0 ? substr(date('d', strtotime($this->full_date)), 1) : date('d', strtotime($this->full_date));
        $this->month = substr(date('m', strtotime($this->full_date)), 0, 1) == 0 ? substr(date('m', strtotime($this->full_date)), 1) : date('m', strtotime($this->full_date));
        $this->year = date('Y', strtotime($this->full_date));
        
        $this->day_title = TranslationHandler::get_static_text("WEEK_DAY_" . strtoupper(date('l', strtotime($this->full_date))));
        $this->month_title = TranslationHandler::get_static_text(strtoupper(month_num_to_string(date('m', strtotime($this->full_date)))));
    }
    
    private function validate_date($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
?>