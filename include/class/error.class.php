<?php
class Error {
    public $title;
    public $text;
    public $code;
    
    public function __construct($title = null, $text = null, $code = null) {
        $this->title = $title;
        $this->text = $text;
        $this->code = $code;
    }
}

