<?php

class ContactHandler extends Handler
{
    public function __construct()
    {
        parent::__construct();
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
