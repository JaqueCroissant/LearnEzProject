<?php
class Handler {
    public $_user;
    public $error;
    
    public function __construct() {
        $this->get_user_object();
    }
    
    protected function get_user_object() {
        if(SessionKeyHandler::session_exists("user")) {
            $this->_user = SessionKeyHandler::get_from_session("user", true);
            return;
        }
        $this->_user = null;
    }
    
    protected function user_exists() {
        $this->get_user_object();
        return !empty($this->_user);
    }
}