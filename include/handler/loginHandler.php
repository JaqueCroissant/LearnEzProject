<?php
class LoginHandler 
{
    private $_user;
    private $_username;
    private $_password;
    
    private $_access;
    private $_token;
    public $error;
    
    public function __construct() 
    {
	$this->_errors	= array();
	$this->_access	= false;
    }
    
    private function assign_properties($username, $password, $token) {
        $this->_token	= isset($token) ? $token : null;
        $this->_username = $username;
        $this->_password = $password;
    }
    
    private function verify_login()
    {
	try 
	{
	    if(!$this->token_valid()) {
		throw new Exception ("LOGIN_INVALID_FORM");
	    }
	    
	    if(empty($this->_username) || empty($this->_password)) {
		throw new Exception ("LOGIN_EMPTY_FORM");
	    }
            
            if($this->login_exists()) {
		throw new Exception ("LOGIN_ALREADY_EXISTS");
	    }
            
            if(!$this->verify_username()) {
                throw new Exception ("LOGIN_INVALID_USERNAME");
            }
	    
            if(!$this->verify_password()) {
                throw new Exception ("LOGIN_INVALID_PASSWORD");
            }
	    
	    $this->_access = true;
	    $this->register_login_session();
	}
	catch (Exception $ex) 
	{
            $this->error = ErrorHandler::ReturnError($ex->getMessage());
	}
    }
    
    private function verify_username() {
        if(empty($this->_username) || !preg_match('/^[a-zA-Z0-9]+$/', $this->_username)) {
            return false;
        }
        
        if(DbHandler::getInstance()->CountQuery("SELECT id FROM users WHERE username = :username LIMIT 1", strtolower($this->_username)) < 1) {
             return false;
        }
        
        return true;
    }
    
    private function verify_password() {
        if(empty($this->_password)) {
            return false;
        }
        
        $userData = DbHandler::getInstance()->ReturnQuery("SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1", strtolower($this->_username), $this->_password);
        
        if(empty($userData)) {
             return false;
        }

        $this->assign_user_login();
        return true;
    }
    
    private function assign_user_login() {
        
    }
    
    private function token_valid()
    {
	return (!SessionKeyHandler::SessionExists("login_token") || SessionKeyHandler::GetFromSession("login_token") != $this->_token) ? false : true;
    }
    
    private function register_login_session()
    {
        if(empty($this->_user)) {
            return;
        }
        SessionKeyHandler::AddToSession("user", $this->_user);
    }
    
    
    public function generate_login_token() {
        if(SessionKeyHandler::SessionExists("login_token")) {
            return SessionKeyHandler::GetFromSession("login_token");
        }
        return reset(SessionKeyHandler::AddToSession("login_token", md5(uniqid(mt_rand(), true))));
    }
    
    private function login_exists() 
    {
	return SessionKeyHandler::SessionExists("user");
    }
    
    private function verify_login_session()
    {
	if($this->login_exists()){
	    $this->_access = true;
	}
    }
    
    public function get_user_session() 
    {
	if($this->login_exists()) {
            return SessionKeyHandler::GetFromSession("user");
        }
    }
    
    public function log_out()
    {
	if($this->login_exists()) 
	{
	    SessionKeyHandler::RemoveFromSession("login_token");
	    return true;
	}
        return false;
    }
    
    public function check_login($username = null, $password = null, $token = null) 
    {
        if(isset($username) && isset($password) && isset($token)) {
            $this->assign_properties($username, $password, $token);
            $this->verify_login();       
        } else {
            $this->verify_login_session();
        }
	return $this->_access;
    }
}

?>
