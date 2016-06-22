<?php
class Error {
    public $title;
    public $text;
    
    public function __construct($title = null, $text = null) {
        $this->title = $title;
        $this->text = $text;
    }
}

