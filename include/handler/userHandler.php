<?php
class UserHandler extends Handler
{
    public $temp_user;
    public $temp_user_array;


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
        return preg_match('/^[a-zA-Z]+$/', $string);
    }

    private function is_valid_input_with_num($string)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $string);
    }

    public function validate_user_information($firstname, $surname, $email = null)
    {
        $new_user = new User();

        try
        {
            if(empty($firstname) || empty($surname))
            {
                throw new Exception("USER_EMPTY_USERNAME_INPUT");
            }

            $this->check_if_valid_string($firstname, false);
            $this->check_if_valid_string($surname, false);

            if(!empty($email))
            {
                $this->check_if_email($email);
                $new_user->email = $email;
            }
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }

        $new_user->firstname = $firstname;
        $new_user->surname = $surname;
        
        return $new_user;
    }

    public function validate_user_affiliations($user_object, $user_type, $school_id = null, $class_ids = null)
    {
        try
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
                verify_class_ids($class_ids);
                $user_object->class_ids = $class_ids;
            }
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
        
        $user_object->user_type_id = $user_type;
        $user_object->school_id = $school_id;

        $this->create_user($user_object);

        return true;
    }

    private function verify_class_ids($class_ids)
    {
        $query = "SELECT id FROM class WHERE ";
        for($i=0; $i<count($class_ids); $i++)
        {
            if($i != 0 && $i!=count($class_ids))
            {
                $insert_values .= " OR ";
            }

            if(!is_numeric($class_ids[$i]))
            {
                throw new Exception("USER_INVALID_CLASS_ID");
            }
            $query .= "id = " . $class_ids[$i];
        }

        $count = DbHandler::get_instance()->count_query($query);
        if($count < count($class_ids))
        {
            throw new Exception("USER_INVALID_CLASS_ID");
        }

        return $class_ids;
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

    private function delete_user($user_object)
    {
        try
        {
            if(!DbHandler::get_instance()->query("DELETE FROM users WHERE id = :id", $user_object->id))
            {
                throw new Exception("DATABASE_UNKNOWN_ERROR");
            }
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function create_user($user_object)
    {
        $user_object->username = $this->generate_username($user_object->firstname, $user_object->surname);
        try
        {
            if(!DbHandler::get_instance()->query("INSERT INTO users (username, user_type_id,
                                            school_id, email, firstname, surname, time_created) VALUES
                                            (:username, :user_id, :school_id, :email, :firstname, :surname, :time_created)",
                                            $user_object->username, $user_object->user_type_id, 
                                            $user_object->school_id, $user_object->email,
                                            $user_object->firstname, $user_object->surname, date ("Y-m-d H:i:s")))
                                            {
                                                throw new Exception("USER_COULDNT_CREATE");
                                            }
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function assign_passwords($user_array)
    {
        try
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
            $this->temp_user_array = array();
            $this->temp_user_array = $user_array;
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function edit_user_info($firstname, $surname, $email, $description, $image)
    {
        try
        {
            if(!$this->user_exists())
            {
                $this->get_user_object();
                if(!$this->user_exists())
                {
                    throw new Exception("USER_DOESNT_EXIST");
                }
            }

            if(!empty($firstname))
            {
                $this->check_if_valid_string($firstname, false);
                $this->_user->firstname = $firstname;
            }

            if(!empty($surname))
            {
                $this->check_if_valid_string($surname, false);
                $this->_user->surname = $surname;
            }

            if(!empty($description))
            {
                $this->check_if_valid_string($description, true);
                $this->_user->description = $description;
            }

            if(!empty($email))
            {
                $this->check_if_email($email);
                $this->_user->email = $email;
            }

            if(!empty($image))
            {
                if(!is_numeric($image))
                {
                    throw new Exception("USER_INVALID_IMAGE_ID");
                }
                $this->_user->image_id = $image;
            }

            foreach(get_object_vars($this->_user) as $key => $value)
            {
                if(!isset($key))
                {
                    $value = "";
                }
            }

            if(!DbHandler::get_instance()->query("UPDATE users SET firstname = :firstname,
                                                  surname = :surname, description = :description,
                                                  email = :email, image_id = :image WHERE id = :id",
                                                  $this->_user->firstname, $this->_user->surname, $this->_user->description,
                                                  $this->_user->email, $this->_user->image_id, $this->_user->id))
            {
                throw new Exception("DATABASE_UNKNOWN_ERROR");
            }

            if(SessionKeyHandler::session_exists("user"))
            {
                SessionKeyHandler::remove_from_session("user");
            }

            SessionKeyHandler::add_to_session("user", $this->_user, true);

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function check_if_email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new Exception("EMAIL_HAS_WRONG_FORMAT");
        }
    }

    private function check_if_valid_string($string, $allow_special_characters)
    {
        if(!$allow_special_characters)
        {
            if(!$this->is_valid_input($string))
            {
                throw new Exception("USER_INVALID_USERNAME_INPUT");
            }
        }

        if(!is_string($string))
        {
            throw new Exception("USER_INVALID_DESCRIPTION");
        }
    }

    public function get_users($ids)
    {
        try
        {
            if(is_array($ids))
            {
                 $this->get_multiple_users($ids);
            }
            elseif(is_numeric($ids))
            {
                $this->get_single_user($ids);
            }
            else
            {
                throw new Exception("USER_INVALID_ID");
            }
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }

        return true;
    }

    private function get_multiple_users($ids)
    {
        $query = "SELECT * FROM users WHERE id IN (";
        for($i=0; $i<count($ids); $i++)
        {
            $user = $ids[$i];
            if($i != 0 && $i!=count($ids))
            {
                $query .= ", ";
            }

            if(!is_numeric($ids[$i]))
            {
                throw new Exception("USER_INVALID_ID");
            }
            $query .=  $ids[$i];
        }

        $query .= ")";
        $user_data = DbHandler::get_instance()->return_query($query);
        if(count($user_data > 0))
        {
            $this->temp_user_array = array();
            foreach ($user_data as $user)
            {

                $this->temp_user_array[] = new User($user);
            }

        }
        else
        {
            unset($this->temp_user_array);
        }

    }

    private function get_single_user($id)
    {
        $user_data = DbHandler::get_instance()->return_query("SELECT * FROM users WHERE id = :id", $id);
        $this->temp_user = isset($user_data) ? new User(reset($user_data)) : NULL;
    }

    public function import_users($csv_file)
    {
        //try
        //{
            $this->check_if_csv($csv_file);
            $is_first = true;
            $offset = 0;
            $file = fopen($csv_file,"r");

            while(!feof($file))
            {
                $row = fgetcsv($file, 0, ";",",");

                if($is_first)
                {
                    $offset = $this->validate_csv_columns($row);
                    $is_first = false;
                }
                else
                {
                    $this->validate_csv_content($row, $offset);
                }
            }

            fclose($file);

            return true;
        //}
        /*catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }*/

    }

    private function validate_csv_content($row, $offset)
    {
        if(empty($row[0+$offset]) || empty($row[1+$offset]) || empty($row[2+$offset]))
        {
            throw new Exception("IMPORT_MISSING_VALUE");
        }

        $this->check_if_valid_string($row[0+$offset], false);
        $this->check_if_valid_string($row[1+$offset], false);

        if(!empty($row[3+$offset]))
        {
            $this->check_if_email($row[3+$offset]);
        }

        if(!empty($row[4+$offset]))
        {
            if(strlen($row[4+$offset]) < 6)
            {
                throw new Exception("IMPORT_INVALID_PASSWORD");
            }
        }
    }

    private function validate_csv_columns($row)
    {
        $count = count($row);
        $offset = 0;

        if($count != 6)
        {
            if($count > 6)
            {
                $offset = $count - 6;
            }
            else
            {
                throw new Exception("IMPORT_INVALID_FORMATTING");
            }
        }

        if($row[0+$offset] != "FIRST NAME" || $row[1+$offset] != "SURNAME"
        || $row[2+$offset] != "USER TYPE" || $row[3+$offset] != "EMAIL"
        || $row[4+$offset] != "PASSWORD" || $row[5+$offset] != "SCHOOL ID")
        {
            throw new Exception("IMPORT_INVALID_FORMATTING");
        }
        return $offset;
    }

    private function check_if_csv($file)
    {
        $info = pathinfo($file);
        if($info['extension']!="csv")
        {
            throw new Exception("IMPORT_INVALID_FORMAT");
        }
    }
}
?>
