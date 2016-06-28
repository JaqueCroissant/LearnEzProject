<?php
class UserHandler extends Handler
{
    public function __construct() {
        parent::__construct();
        $this->get_user_object();
    }
    
    // old password, new password, new password copy
    // user id, validation_code, new password, new password copy
    public function change_password() {
        $user_id = null;
        $username = null;
        
        try
        {
            if(func_num_args() != 3 && func_num_args() != 4) {
                throw new Exception ();
            }
            
            $password_reset = func_num_args() == 4 || !$this->user_exists();
            $new_password = $password_reset ? func_get_args()[2] : func_get_args()[1];
            $new_password_copy = $password_reset ? func_get_args()[3] : func_get_args()[2];
            

            if(empty($new_password) || empty($new_password_copy)){
                throw new Exception ("USER_EMPTY_FORM");
            }
            
            if($new_password != $new_password_copy) {
                throw new Exception ("USER_PASSWORDS_DOES_NOT_MATCH");
            }
            
            if(strlen($new_password) < 6) {
                throw new Exception ("USER_PASSWORD_TOO_SHORT");
            }
            
            if($password_reset && !$this->user_exists()) {
                $user_id = func_get_args()[0];
                $validation_code = func_get_args()[1];
                
                if(!is_numeric($user_id)) {
                    throw new Exception ("USER_INVALID_ID");
                }
                
                $userData = DbHandler::getInstance()->ReturnQuery("SELECT username FROM users WHERE id = :user_id AND validation_code = :validation_code", $user_id, $validation_code);
                if(empty($userData)) {
                    throw new Exception ("USER_INVALID_PASSWORD_RESET");
                }
                
                $username = reset($userData)["username"];
            }
            
            if(!$password_reset && $this->user_exists()) {
                $old_password = func_get_args()[0];
                $username = $this->_user->username;
                
                if(empty($old_password)) {
                    throw new Exception ("USER_EMPTY_FORM");
                }
                
                if(DbHandler::getInstance()->CountQuery("SELECT id FROM users WHERE id = :id AND password = :password LIMIT 1", $this->_user->id, hash("sha256", $old_password . " " . $this->_user->username)) < 1) {
                    throw new Exception ("USER_INVALID_OLD_PASSWORD");
                }
            }
            
            $user_id = !$password_reset && $this->user_exists() ? $this->_user->id : $user_id;
            DbHandler::getInstance()->Query("UPDATE users SET password = :password, validation_code = :validation_code WHERE id = :id", hash("sha256", $new_password . " " . $username), null, $user_id);
            
            return true;
        }
        catch (Exception $ex)
        {
            $this->error = ErrorHandler::ReturnError($ex->getMessage());
            return false;
        }
        
    }

    private function is_valid_input($string)
    {
        return preg_match('/^[a-zA-Z]+$/', $string);
    }

    public function validate_information()
    {
        if(empty($firstname) || empty($surname))
        {
            throw new Exception("USER_EMPTY_USERNAME_INPUT");
        }

        if(!is_string($firstname) || !is_string($surname))
        {
            throw new Exception("USER_INVALID_USERNAME_INPUT");
        }

        if(is_valid_input($firstname) || is_valid_input($surname))
        {
            throw new Exception("USER_INVALID_USERNAME_INPUT");
        }

        //returner bruger med nye oplysninger
    }

    public function generate_username($firstname, $surname)
    {
        $firstname = strtolower($firstname);
        $surname = strtolower($surname);
        $new_username = "";
        $conc_name = "";

        if(strlen($firstname) < 4)
        {
            $new_username .= $firstname;
            $diff = 4 - strlen($firstname);
            
            if(strlen($surname)<$diff)
            {
                $new_username .= $surname;
            }
            else
            {
                $new_username .= substr($surname, 0, $diff);
            }
        }
        else
        {
            $new_username .= substr($firstname, 0, 4);
        }

        do
        {
            $conc_name = $new_username .= $this->add_random_elements(); 
        }
        while($this->username_exists($conc_name));

        return $conc_name;
    }

    private function add_random_elements()
    {
        $elements = "";
        for($i = 0; $i < 4; $i++)
        {
            if($i==3)
            {
                $elements .= $this->random_char();
            }
            else
            {
                $elements .= rand(0,9);
            }
        }
        return $elements;
    }

    private function random_char()
    {
        $int = rand(0,36);
        $a_z = "abcdefghijklmnopqrstuvwxyz1234567890";
        $rand_letter = $a_z[$int];
        return $rand_letter;
    }

    private function username_exists($username)
    {
        $count = DbHandler::getInstance()->CountQuery("SELECT * FROM users WHERE username = :name", $username);
        return $count > 1;
    }

    public function create_user()
    {

    }
}
?>
