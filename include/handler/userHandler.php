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
                
                $userData = DbHandler::get_instance()->return_query("SELECT username FROM users WHERE id = :user_id AND validation_code = :validation_code", $user_id, $validation_code);
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
                
                if(DbHandler::get_instance()->count_query("SELECT id FROM users WHERE id = :id AND password = :password LIMIT 1", $this->_user->id, hash("sha256", $old_password . " " . $this->_user->username)) < 1) {
                    throw new Exception ("USER_INVALID_OLD_PASSWORD");
                }
            }
            
            $user_id = !$password_reset && $this->user_exists() ? $this->_user->id : $user_id;
            DbHandler::get_instance()->query("UPDATE users SET password = :password, validation_code = :validation_code WHERE id = :id", hash("sha256", $new_password . " " . $username), null, $user_id);
            
            return true;
        }
        catch (Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
        
    }

    private function is_valid_input($string)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $string);
    }

    public function validate_user_information($firstname, $surname, $email = null)
    {
        $new_user = new User();

        if(empty($firstname) || empty($surname))
        {
            throw new Exception("USER_EMPTY_USERNAME_INPUT");
        }

        if(!is_string($firstname) || !is_string($surname))
        {
            throw new Exception("USER_INVALID_USERNAME_INPUT");
        }

        if(!$this->is_valid_input($firstname) || !$this->is_valid_input($surname))
        {
            throw new Exception("USER_INVALID_USERNAME_INPUT");
        }

        if(!empty($email))
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                throw new Exception("EMAIL_HAS_WRONG_FORMAT");
            }
            else
            {
                $new_user->email = $email;
            }
        }

        $new_user->firstname = $firstname;
        $new_user->surname = $surname;
        
        return $new_user;
    }

    public function validate_user_affiliations($user_object, $user_type, $school_id = null, $class_ids = null)
    {
        if(!$this->user_exists())
        {
            throw new Exception("USER_DOESNT_EXIST");
        }
        
        if(!is_numeric($user_type) || $user_type < 1 || $user_type > 4)
        {
            throw new Exception("USER_INVALID_TYPE");
        }

        if(!empty($school_id) && !is_numeric($school_id))
        {
            throw new Exception("USER_INVALID_SCHOOL_ID");
        }

        if($user_type != 1 && empty($school_id))
        {
            throw new Exception("USER_INVALID_SCHOOL_ID");
        }

        if($user_type != 1 && $count < 1)
        {
            throw new Exception("USER_INVALID_SCHOOL_ID");
        }
    
        if(!empty($class_ids))
        {
            $query = "SELECT id FROM class WHERE ";   
            for($i=0; $i<count($class_ids); $i++)
            {
                $class = $class_ids[$i];
                if($i != 0 && $i!=count($class_ids))
                {
                    $insert_values .= " OR ";
                }

                if(is_numeric($class['id']))
                {
                    throw new Exception("USER_INVALID_CLASS_ID");
                }
                $query .= "id = " . $class['id'];
            }

            $count = DbHandler::get_instance()->count_query($query);
            if($count < count($class_ids))
            {
                throw new Exception("USER_INVALID_CLASS_ID");
            }

            $user_object->class_ids = $class_ids;
        }
        
        $user_object->user_type_id = $user_type;
        $user_object->school_id = $school_id;

        $this->create_user($user_object);
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
                $elements .= $this->random_char(1);
            }
            else
            {
                $elements .= rand(0,9);
            }
        }
        return $elements;
    }

    private function random_char($iterations)
    {
        $int = rand(0,36);
        $a_z = "abcdefghijklmnopqrstuvwxyz1234567890";
        $rand_letter = "";
        
        for($i=0; $i<$iterations; $i++)
        {
            $rand_letter .= $a_z[$int];
        }

        return $rand_letter;
    }

    private function username_exists($username)
    {
        $count = DbHandler::get_instance()->count_query("SELECT * FROM users WHERE username = :name", $username);
        return $count > 1;
    }

    private function create_user($user_object)
    {
        $user_object->username = $this->generate_username($user_object->firstname, $user_object->surname);
        
        
        if(!DbHandler::get_instance()->query("INSERT INTO users (username, user_type_id, 
                                            school_id, email, firstname, surname) VALUES 
                                            (:username, :user_id, :school_id, :email, :firstname, :surname)", 
                                            $user_object->username, $user_object->user_type_id, 
                                            $user_object->school_id, $user_object->email,
                                            $user_object->firstname, $user_object->surname))
                                            {
                                                throw new Exception("USER_COULDNT_CREATE");
                                            }
        echo "User created!";
    }

    public function assign_passwords($user_array)
    {
        foreach ($user_array as $user) 
        {
            $user->unhashed_password = $this->random_char(8);
            $hashed_password = hash("sha256", $user->unhashed_password . " " . $user_object->username);
            if(!DbHandler::get_instance()->query("INSERT INTO users (password) VALUES (:password) 
                                                WHERE id = :id", $hashed_password, $user->id))
                                                {
                                                    throw new Exception("PASSWORD_COULDNT_ASSIGN");
                                                }
        }

        return $user_array;
    }
}
?>
