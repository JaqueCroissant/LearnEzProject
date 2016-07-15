<?php
class LoginHandler 
{
    private $_user;
    private $_username;
    private $_password;
    private $_email;
    
    private $_access;
    private $_token;
    public $error;
    
    public function __construct() 
    {
        $this->_errors	= array();
        $this->_access	= false;
        $this->generate_login_token();
    }
    
    private function assign_properties($username, $password, $token) 
    {
        $this->_token	= isset($token) ? $token : null;
        $this->_username = $username;
        $this->_password = $password;
    }
    
    private function verify_login()
    {
        try
        {
            if(!$this->token_valid()) 
            {
                throw new Exception ("LOGIN_INVALID_FORM");
            }

            if(empty($this->_username) || empty($this->_password)) 
            {
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
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
    }
    
    private function verify_username() {
        if(empty($this->_username) || !preg_match('/^[a-zA-Z0-9]+$/', $this->_username)) {
            return false;
        }
        
        if(DbHandler::get_instance()->count_query("SELECT id FROM users WHERE username = :username LIMIT 1", strtolower($this->_username)) < 1) {
             return false;
        }
        
        return true;
    }

    private function verify_email()
    {
        if (!filter_var($this->_email, FILTER_VALIDATE_EMAIL)) {
             return false;
        }
        
        
        $user = DbHandler::get_instance()->return_query("SELECT users.*, translation_user_type.title as user_type_title FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id WHERE email = :email", $this->_email);
           
        if(empty($user)) {
            return false;
        }
        
        $this->_user = new User(reset($user));
        return true;
    }
    
    private function verify_password() {
        if(empty($this->_password)) {
            return false;
        }
        
        $userData = DbHandler::get_instance()->return_query("SELECT users.*, translation_user_type.title as user_type_title FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id WHERE username = :username AND password = :password LIMIT 1", strtolower($this->_username), hash("sha256", $this->_password . " " . $this->_username));
        
        if(empty($userData)) {
             return false;
        }

        $this->_user = new User(reset($userData));
        DbHandler::get_instance()->query("UPDATE users SET last_login = :date WHERE id = :id", date ("Y-m-d H:i:s"), $this->_user->id);
        return true;
    }
    
    private function token_valid()
    {
	return (!SessionKeyHandler::session_exists("login_token") || SessionKeyHandler::get_from_session("login_token") != $this->_token) ? false : true;
    }
    
    private function register_login_session()
    {
        if(empty($this->_user)) {
            return;
        }
        
        SessionKeyHandler::add_to_session("user", $this->_user, true);
    }
    
    
    private function generate_login_token() {
        if(!SessionKeyHandler::session_exists("login_token")) {
            SessionKeyHandler::add_to_session("login_token", md5(uniqid(mt_rand(), true)));
        }
    }
    
    public function get_login_token() {
        if(SessionKeyHandler::session_exists("login_token")) {
            return SessionKeyHandler::get_from_session("login_token");
        }
        return reset(SessionKeyHandler::add_to_session("login_token", md5(uniqid(mt_rand(), true))));
    }
    
    private function login_exists() 
    {
	return SessionKeyHandler::session_exists("user");
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
            return SessionKeyHandler::get_from_session("user", true);
        }
    }
    
    public function log_out()
    {
	if($this->login_exists()) 
	{
	    SessionKeyHandler::remove_from_session("user");
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
    
    public function reset_password($email = null)
    {
        $this->_email = $email;
        try
        {
            if(empty($this->_email)){
                throw new Exception ("LOGIN_INVALID_EMAIL");
            }

            if(!$this->verify_email()){
                throw new Exception ("LOGIN_INVALID_EMAIL");
            }

            if(strtotime($this->_user->last_password_request) > strtotime("-15 minutes")){
                throw new Exception("LOGIN_INVALID_TIME");
            }
            
            // mail function
            
            DbHandler::get_instance()->query("UPDATE users SET last_password_request = :date, validation_code = :validation_code WHERE id = :id", date ("Y-m-d H:i:s"), md5(uniqid(mt_rand(), true)), $this->_user->id);
            return true;
        }
        catch (Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function validate_reset_password($user_id = null, $validation_code = null) {
        try
        {
            if(empty($validation_code) || empty($user_id) || !is_numeric($user_id)){
                throw new Exception ("LOGIN_INVALID_VALIDATION_CODE");
            }
            $user_data = DbHandler::get_instance()->return_query("SELECT * FROM users WHERE id = :id AND validation_code = :validation_code", $user_id, $validation_code);
            
            if(empty($user_data)){
                throw new Exception ("LOGIN_INVALID_VALIDATION_CODE");
            }
            
            $user = new User(reset($user_data));
            
            if(empty($user->validation_code)){
                throw new Exception ("LOGIN_INVALID_VALIDATION_CODE");
            }

            if(strtotime($user->last_password_request) < strtotime("-15 minutes")){
                throw new Exception("LOGIN_INVALID_VALIDATION_TIME");
            }
            return true;
        }
        catch (Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
}

?>
