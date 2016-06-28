<?php

class Handler {
    protected $_user;
    public $_error;
    
    public function __construct() {
        $this->get_user_object();
    }
    
    protected function get_user_object() {
        if(SessionKeyHandler::SessionExists("user")) {
            $this->_user = SessionKeyHandler::GetFromSession("user", true);
            return;
        }
        $this->_user = null;
    }
    
    protected function user_exists() {
        $this->get_user_object();
        return !empty($this->_user);
    }
}