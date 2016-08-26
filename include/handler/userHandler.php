<?php
class UserHandler extends Handler
{
    public $users = array();
    public $temp_user_array;
    public $temp_user;
    public $new_username;
    public $profile_images;
    public $import_add_info;
    public $import_has_add_info;

    public function __construct() {
        parent::__construct();
        $this->get_user_object();
        $this->current_user = $this->_user;
        
    }
    
    // old password, new password, new password copy
    // user id, validation_code, new password, new password copy
    public function change_password()
    {
        $user_id = null;
        $username = null;
        
        try
        {
            if($this->user_exists() && !RightsHandler::has_user_right("CHANGE_PASSWORD"))
            {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

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
        return preg_match("/^[a-zA-ZÆØÅæøåÄËÏÖÜäëïöüï '-]+$/i", $string);
    }

    private function is_valid_input_with_num($string)
    {
        return preg_match("/^[a-zA-Z0-9ÆØÅæøåÄËÏÖÜäëïöüï '-]+$/i", $string);
    }

    private function clean($string)
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    public function create_new_profile($firstname, $surname, $email, $password, $usertype, $school_id, $class_ids)
    {
        try
        {
            $this->validate_user_logged_in();

            if(!RightsHandler::has_user_right("ACCOUNT_CREATE"))
            {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            $this->validate_user_information($firstname, $surname, $email, $password);
            $this->validate_user_affiliations($this->check_if_valid_type($usertype), $school_id, $class_ids);

            $new_user = new User();
            $new_user->firstname = $firstname;
            $new_user->surname = $surname;
            $new_user->email = $email;
            $new_user->user_type_id = $this->check_if_valid_type($usertype);
            $new_user->class_ids = $class_ids;

            if($this->_user->user_type_id > 1)
            {
                $new_user->school_id = $this->_user->school_id;
            }
            else
            {
                $new_user->school_id = $school_id;
            }
            
            $this->users = array();
            
            if(!empty($password))
            {
                $new_user->unhashed_password = $password;
                $this->create_user_with_password($new_user, true);
            }
            else
            {
                $this->create_user($new_user, false);
            }

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function validate_user_information($firstname, $surname, $email = null, $password = null)
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
            $this->mail_exists($email);
        }

        if(!empty($password))
        {
            if(strlen($password) < 6)
            {
                throw new Exception("IMPORT_INVALID_PASSWORD");
            }
        }
    }

    private function validate_user_affiliations($user_type, $school_id = null, $class_ids = null)
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

        if(!empty($class_ids))
        {
            $this->verify_class_ids($class_ids);
        }
    }

    private function verify_class_ids($class_ids)
    {
        if(is_array($class_ids))
        {
            foreach($class_ids as $id)
            {
                if(!is_numeric($id))
                {
                    throw new Exception("USER_INVALID_CLASS_ID");
                }
            }
        }
        else
        {
            if(!is_numeric($class_ids))
            {
                throw new Exception("USER_INVALID_CLASS_ID");
            }
        }
    }

    private function generate_username($firstname, $surname)
    {
        $firstname = $this->clean(strtolower($firstname));
        $surname = $this->clean(strtolower($surname));
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

        while(true) {
            $conc_name = $new_username .''. $this->add_random_elements();

            if(!$this->username_exists($conc_name)) {
                break;
            }
        }
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

    public function random_char($iterations)
    {
        $a_z = "abcdefghijklmnopqrstuvwxyz1234567890";
        $rand_letter = "";
        
        for($i=0; $i<$iterations; $i++)
        {
            $rand_letter .= $a_z{rand(0, strlen($a_z)-1)};
        }

        return $rand_letter;
    }

    private function username_exists($username)
    {
        $count = DbHandler::get_instance()->count_query("SELECT id FROM users WHERE username = :name", $username);
        return $count > 0;
    }

    public function set_user_availability($user_id)
    {
        try
        {
            $this->validate_user_logged_in();

            if(!RightsHandler::has_user_right("ACCOUNT_AVAILABILITY"))
            {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($user_id) || !is_numeric($user_id))
            {
                throw new Exception("INVALID_INPUT");
            }

            if(!RightsHandler::has_user_right("SCHOOL_FIND"))
            {
                $count = DbHandler::get_instance()->count_query("SELECT id FROM users WHERE id = :id AND school_id = :school", $user_id, $this->_user->school_id);

                if($count != 1)
                {
                    throw new Exception("INSUFFICIENT_RIGHTS");
                }
            }

            if(!DbHandler::get_instance()->query("UPDATE users SET open = NOT open WHERE id = :id", $user_id))
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

    public function delete_user($user_id)
    {
        try
        {
            $this->validate_user_logged_in();

            if(!RightsHandler::has_user_right("ACCOUNT_DELETE"))
            {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($user_id) || !is_numeric($user_id))
            {
                throw new Exception("INVALID_INPUT");
            }

            $this->get_user_by_id($user_id);

            $queries = array();
            $queries[] = "DELETE FROM users WHERE id = :id";
            $queries[] = "DELETE FROM user_class WHERE users_id = :id";
            $queries[] = "DELETE FROM user_course_lecture WHERE user_id = :id";
            $queries[] = "DELETE FROM user_course_test WHERE user_id = :id";
            $queries[] = "DELETE FROM user_notifications WHERE user_id = :id";
            $queries[] = "DELETE FROM user_settings WHERE user_id = :id";
            $queries[] = "DELETE FROM certificates WHERE user_id = :id";
            $queries[] = "DELETE FROM user_achievement WHERE users_id = :id";
            $queries[] = "DELETE FROM image WHERE user_id = :id";

            foreach($queries as $query)
            {
                if(!DbHandler::get_instance()->query($query, $user_id))
                {
                    throw new Exception("DATABASE_UNKNOWN_ERROR");
                }
            }

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function create_class_affiliation($classes, $id = null)
    {
        $user_id = isset($id) ? $id : DbHandler::get_instance()->last_inserted_id();
        $query = "INSERT INTO user_class (users_id, class_id) VALUES ";

        if(count($classes)>0)
        {
            for($i = 0; $i < count($classes); $i++)
            {
                if($i > 0 && $i < count($classes))
                {
                    $query .= ", ";
                }

                $query .= "(" . $user_id . ", " . $classes[$i] . ")";
            }
            DbHandler::get_instance()->query($query);
        }
    }

    private function create_user($user_object, $add_to_user_array)
    {  
        $username = $this->generate_username($user_object->firstname, $user_object->surname);
        $user_object->username = $username;
        $this->new_username = $username;
        try
        {
            if(!DbHandler::get_instance()->query("INSERT INTO users (username, user_type_id,
                                            school_id, email, firstname, surname, time_created, open) VALUES
                                            (:username, :user_id, :school_id, :email, :firstname, :surname, :time_created, :open)",
                                            $user_object->username, $user_object->user_type_id, 
                                            $user_object->school_id, $user_object->email,
                                            $user_object->firstname, $user_object->surname, date ("Y-m-d H:i:s"), 0))
                                            {
                                                throw new Exception("USER_COULDNT_CREATE");
                                            }
            $latest_id = DbHandler::get_instance()->last_inserted_id();
            DbHandler::get_instance()->query("INSERT INTO user_settings (user_id) VALUES (:user_id)", $latest_id);
            $user_object->unhashed_password = "";

            $this->create_class_affiliation($user_object->class_ids, $latest_id);

            if($add_to_user_array)
            {
                $this->users[] = $user_object;
            }
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function create_user_with_password($user_object, $add_to_user_array)
    {
        $username = $this->generate_username($user_object->firstname, $user_object->surname);
        $user_object->username = $username;
        $this->new_username = $username;

        $password = hash("sha256", $user_object->unhashed_password . " " . $user_object->username);
        try
        {
            if(!DbHandler::get_instance()->query("INSERT INTO users (username, user_type_id,
                                            school_id, email, firstname, surname, password, time_created, open) VALUES
                                            (:username, :user_id, :school_id, :email, :firstname, :surname, :password, :time_created, :open)",
                                            $user_object->username, $user_object->user_type_id,
                                            $user_object->school_id, $user_object->email,
                                            $user_object->firstname, $user_object->surname, $password, date ("Y-m-d H:i:s"), 1))
                                            {
                                                throw new Exception("USER_COULDNT_CREATE");
                                            }

            $latest_id = DbHandler::get_instance()->last_inserted_id();
            DbHandler::get_instance()->query("INSERT INTO user_settings (user_id) VALUES (:user_id)", $latest_id);
            
            $this->create_class_affiliation($user_object->class_ids, $latest_id);

            if($add_to_user_array)
            {
                $this->users[] = $user_object;
            }

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function assign_passwords($user_ids)
    {
        try
        {
            $query = "";
            $temp_users = "";
            $this->validate_user_logged_in();

            if(!is_array($user_ids))
            {
                throw new Exception("INVALID_INPUT");
            }

            if(count($user_ids) < 1)
            {
                throw new Exception("ACCOUNT_NO_SELECTION");
            }

            foreach($user_ids as $id)
            {
                if(!is_numeric($id))
                {
                    throw new Exception("INVALID_INPUT");
                }
            }

            $can_assign_global = RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD");
            $can_assign_student = RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD");


            if(!$can_assign_global && !$can_assign_student)
            {
                    throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if(!RightsHandler::has_user_right("SCHOOL_FIND"))
            {
                if(!RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") && RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD"))
                {
                    $query = "SELECT * FROM users WHERE id IN (" . generate_in_query($user_ids) . ") AND school_id = :school_id AND user_type_id = :type_id";
                    $temp_users = DbHandler::get_instance()->return_query($query, $this->_user->school_id, 4);
                }
                else
                {
                    $query = "SELECT * FROM users WHERE id IN (" . generate_in_query($user_ids) . ") AND school_id = :school_id AND user_type_id >= :type_id";
                    $temp_users = DbHandler::get_instance()->return_query($query, $this->_user->school_id, $this->_user->user_type_id);
                }
            }
            else
            {
                $query = "SELECT * FROM users WHERE id IN (" . generate_in_query($user_ids) . ")";
                $temp_users = DbHandler::get_instance()->return_query($query);
            }

            if(count($user_ids) != count($temp_users))
            {
                throw new Exception("INVALID_INPUT");
            }

            $user_array = array();
            foreach ($temp_users as $user)
            {
                $user_array[] = new User($user);
            }

            $this->assign_new_password($user_array);

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function assign_new_password($user_array)
    {
        $users = array();

        foreach ($user_array as $user)
        {
            $user->unhashed_password = $this->random_char(8);
            $hashed_password = hash("sha256", $user->unhashed_password . " " . $user->username);
            if(!DbHandler::get_instance()->query("UPDATE users SET password = :password
            WHERE id = :id", $hashed_password, $user->id))
            {
                throw new Exception("PASSWORD_COULDNT_ASSIGN");
            }

            $users[$user->id] = $user->unhashed_password;
        }
        $this->temp_user_array = array();
        $this->temp_user_array = $users;
    }

    public function init_user_info($email, $password, $password_copy)
    {
        try
        {
            $has_password = (isset($password) && isset($password_copy)) && (!empty($password) && !empty($password_copy));
            $this->check_if_email($email);

            if(!SessionKeyHandler::session_exists("user_setup"))
            {
                throw new Exception("INVALID_SETTINGS_INPUT");
            }

            $user = SessionKeyHandler::get_from_session("user_setup");

            if($email != $user['email'])
            {
                $this->mail_exists($email);
            }

            if($has_password)
            {
                if($password != $password_copy) {
                    throw new Exception ("USER_PASSWORDS_DOES_NOT_MATCH");
                }

                if(strlen($password) < 6) {
                    throw new Exception ("USER_PASSWORD_TOO_SHORT");
                }
            }

            if($has_password)
            {
                $hashed_password = hash("sha256", $password . " " . $user['username']);
                if(!DbHandler::get_instance()->query("UPDATE users SET email = :email, password = :password, last_login = :date WHERE id = :id", $email, $hashed_password, date ("Y-m-d H:i:s"),$user['user_id']))
                {
                    throw new Exception("DATABASE_UNKNOWN_ERROR");
                }
            }
            else
            {
                if(!DbHandler::get_instance()->query("UPDATE users SET email = :email, last_login = :date WHERE id = :id", $email, date ("Y-m-d H:i:s"), $user['user_id']))
                {
                    throw new Exception("DATABASE_UNKNOWN_ERROR");
                }
            }
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function edit_user_info($firstname = null, $surname = null, $email = null, $description = null, $image = null)
    {
        try
        {
            $this->validate_user_logged_in();

            if(!empty($firstname) && $firstname != $this->_user->firstname)
            {
                if(!RightsHandler::has_user_right("CHANGE_FULL_NAME"))
                {
                    throw new Exception("INSUFFICIENT_RIGHTS");
                }

                $this->check_if_valid_string($firstname, false);
                $this->_user->firstname = $firstname;
            }

            if(!empty($surname) && $surname != $this->_user->surname)
            {
                if(!RightsHandler::has_user_right("CHANGE_FULL_NAME"))
                {
                    throw new Exception("INSUFFICIENT_RIGHTS");
                }

                $this->check_if_valid_string($surname, false);
                $this->_user->surname = $surname;
            }

            if(!empty($description))
            {
                if(!is_string($description))
                {
                    throw new Exception("USER_INVALID_DESCRIPTION");
                }
                $this->_user->description = $description;
            }

            if(!empty($email))
            {
                $this->check_if_email($email);


                if($email != $this->_user->email)
                {
                    $this->mail_exists($email);
                }

                $this->_user->email = $email;
            }

            if($image == 0 || !empty($image))
            {
                if($image != 0 && !is_numeric($image))
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
            
            $profile_image = DbHandler::get_instance()->return_query("SELECT filename as profile_image FROM image WHERE id = :image_id" , !empty($this->_user->image_id) ? $this->_user->image_id : 0);
            
            $element = !empty($profile_image) ? reset($profile_image)["profile_image"] : null;
            $this->_user->profile_image = $element;
            SessionKeyHandler::add_to_session('user', $this->_user, true);

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function edit_account($user_id, $firstname, $surname, $email, $description, $password, $class_ids = null)
    {
        try
        {
            $this->validate_user_logged_in();

            if(!RightsHandler::has_user_right("ACCOUNT_EDIT"))
            {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if(!$this->get_user_by_id($user_id))
            {
                throw new Exception("USER_DOESNT_EXIST");
            }

            $user = $this->temp_user;
            $this->temp_user = null;

            $has_password = false;
            $has_classes = false;

            if(!empty($firstname) && $firstname != $user->firstname)
            {
                $this->check_if_valid_string($firstname, false);
                $user->firstname = $firstname;
            }

            if(!empty($surname) && $surname != $user->surname)
            {
                $this->check_if_valid_string($surname, false);
                $user->surname = $surname;
            }

            if(!empty($description) && $description != $user->description)
            {
                if(!is_string($description))
                {
                    throw new Exception("USER_INVALID_DESCRIPTION");
                }
                $user->description = $description;
            }

            if(!empty($email) && $email != $user->email)
            {
                $this->check_if_email($email);
                $this->mail_exists($email);
                $user->email = $email;
            }

            if(!empty($password) && strlen($password) > 5)
            {
                $this->is_valid_input_with_num($password);
                $user->unhashed_password = $password;
                $has_password = true;
            }

            if(!empty($class_ids) && $user->user_type_id > 2)
            {
                $has_classes = true;
            }

            foreach(get_object_vars($user) as $key => $value)
            {
                if(!isset($key))
                {
                    $value = "";
                }
            }

            if($has_password)
            {
                $hashed_password = hash("sha256", $user->unhashed_password . " " . $user->username);
                if(!DbHandler::get_instance()->query("UPDATE users SET firstname = :firstname,
                                                  surname = :surname, description = :description,
                                                  email = :email, password = :password WHERE id = :id",
                                                  $user->firstname, $user->surname, $user->description,
                                                  $user->email, $hashed_password, $user->id))
                {
                    throw new Exception("DATABASE_UNKNOWN_ERROR");
                }
            }
            else
            {
                if(!DbHandler::get_instance()->query("UPDATE users SET firstname = :firstname,
                                                  surname = :surname, description = :description,
                                                  email = :email WHERE id = :id",
                                                  $user->firstname, $user->surname, $user->description,
                                                  $user->email, $user->id))
                {
                    throw new Exception("DATABASE_UNKNOWN_ERROR");
                }
            }

            if($has_classes)
            {
                if(!DbHandler::get_instance()->query("DELETE FROM user_class WHERE users_id = :id", $user->id))
                {
                    throw new Exception("DATABASE_UNKNOWN_ERROR");
                }

                $this->create_class_affiliation($class_ids, $user->id);
            }

            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function check_if_email($email, $is_import=false)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            if($is_import)
            {
                $this->import_has_add_info = true;
                throw new Exception("IMPORT_EMAIL_HAS_WRONG_FORMAT");
            }
            else
            {
                throw new Exception("EMAIL_HAS_WRONG_FORMAT");
            }
            
        }
    }

    private function check_if_valid_string($string, $is_import)
    {
        if(!$this->is_valid_input($string))
        {
            if($is_import)
            {
                $this->import_has_add_info = true;
                throw new Exception("IMPORT_INVALID_NAME_INPUT");
            }
            else
            {
                throw new Exception("USER_INVALID_NAME_INPUT");
            }
            
        }
    }
    
    public function get_all_users() {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            if (!RightsHandler::has_user_right("ACCOUNT_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }
            
            $users = array();
            
            if(RightsHandler::has_user_right("SCHOOL_FIND"))
            {
                $query = "SELECT users.*, translation_user_type.title as user_type_title, school.name as school_name FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id LEFT JOIN school ON school.id = users.school_id WHERE translation_user_type.language_id = :language_id";
                $users = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language());
            }
            else
            {
                $query = "SELECT users.*, translation_user_type.title as user_type_title, school.name as school_name FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id INNER JOIN school ON school.id = users.school_id WHERE translation_user_type.language_id = :language_id AND school.id = :school_id";
                $users = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), $this->_user->school_id);
            }

            $this->users = array();
            foreach ($users as $value) {
                $this->users[] = new User($value);
            }

            if (count($this->users) == 0) {
                throw new Exception("NO_USERS_FOUND");
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    public function get_all_users_without_password()
    {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("ACCOUNT_FIND")) {
                throw new Exception("INSUFFICIENT_RIGHTS");
            }

            $users = array();

            if(RightsHandler::has_user_right("SCHOOL_FIND"))
            {
                $query = "SELECT users.*, translation_user_type.title as user_type_title, school.name as school_name FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id LEFT JOIN school ON school.id = users.school_id WHERE translation_user_type.language_id = :language_id AND users.password = ''";
                $users = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language());
            }
            else if(!RightsHandler::has_user_right("SCHOOL_FIND") && RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD"))
            {
                $query = "SELECT users.*, translation_user_type.title as user_type_title, school.name as school_name FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id INNER JOIN school ON school.id = users.school_id WHERE translation_user_type.language_id = :language_id AND school.id = :school_id AND users.password = ''";
                $users = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), $this->_user->school_id);
            }
            else if(!RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") && RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD"))
            {
                $query = "SELECT users.*, translation_user_type.title as user_type_title, school.name as school_name FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id INNER JOIN school ON school.id = users.school_id WHERE translation_user_type.language_id = :language_id AND school.id = :school_id AND users.user_type_id = :user_type_id AND users.password = ''";
                $users = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), $this->_user->school_id, 4);
            }

            $this->users = array();
            foreach ($users as $value) {
                $this->users[] = new User($value);
            }

            if (count($this->users) == 0) {
                throw new Exception("NO_USERS_FOUND");
            }

            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }
    
    public function get_by_school_id($school_id, $student_and_teacher_bool = false, $only_open = true)
    {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(empty($school_id) || !is_numeric($school_id))
            {
                throw new Exception("INVALID_INPUT");
            }
            
            if (!RightsHandler::has_user_right("ACCOUNT_FIND")) {
                        throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if (!RightsHandler::has_user_right("SCHOOL_FIND") && $school_id != $this->_user->school_id) {
                        throw new Exception("INSUFFICIENT_RIGHTS");
            }
            if (!is_bool($student_and_teacher_bool) || !is_bool($only_open)) {
                throw new Exception("INVALID_INPUT");
            }

            $query = "SELECT users.*, translation_user_type.title as user_type_title FROM users INNER JOIN user_type ON user_type.id = users.user_type_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = user_type.id WHERE users.school_id = :school_id AND translation_user_type.language_id = :language_id ";
            if ($student_and_teacher_bool) {
                $query .= "AND users.user_type_id IN (3,4) ";
            } else {
                $query .= "AND users.user_type_id = 4 ";
            }
            if ($only_open) {
                $query .= "AND open = 1";
            }

            $user_data = DbHandler::get_instance()->return_query($query, $school_id, TranslationHandler::get_current_language());
            $this->users = array();
            if(count($user_data > 0))
            {
                foreach ($user_data as $user)
                {
                    $this->users[] = new User($user);
                }
            }
            
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    public function get_by_class_id($class_id, $is_mine = false, $teachers_and_students = false)
    {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if(empty($class_id) || !is_numeric($class_id))
            {
                throw new Exception("INVALID_INPUT");
            }
            
            if(!$is_mine)
            {
                if (!RightsHandler::has_user_right("ACCOUNT_FIND") || !RightsHandler::has_user_right("CLASS_FIND")) 
                {
                    throw new Exception("INSUFFICIENT_RIGHTS");
                }
            }
            else
            {
                $count = DbHandler::get_instance()->count_query("SELECT id FROM user_class WHERE class_id = :class_id AND users_id = :user_id", $class_id, $this->_user->id);
                if($count < 1)
                {
                    throw new Exception("INSUFFICIENT_RIGHTS");
                }
            }
            
            if($teachers_and_students)
            {
                $user_data = DbHandler::get_instance()->return_query("SELECT users.* FROM users INNER JOIN user_class ON users.id = user_class.users_id AND user_class.class_id = :id WHERE users.user_type_id > 2 AND users.open = 1", $class_id);
            }
            else
            {
                $user_data = DbHandler::get_instance()->return_query("SELECT users.* FROM users INNER JOIN user_class ON users.id = user_class.users_id AND user_class.class_id = :id WHERE users.user_type_id > 3 AND users.open = 1", $class_id);
            }
            
            $this->users = array();
            if(count($user_data > 0))
            {
                foreach ($user_data as $user)
                {
                    $this->users[] = new User($user);
                }
            }
            
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    

    public function get_user_by_id($id)
    {
        try
        {
            if(!is_numeric($id))
            {
                throw new Exception("USER_INVALID_ID");
            }

            if(!RightsHandler::has_user_right("SCHOOL_FIND"))
            {
                $user_data = DbHandler::get_instance()->return_query("SELECT users.*, image.filename as profile_image, translation_user_type.title as user_type_title FROM users LEFT JOIN image ON image.id = users.image_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = users.user_type_id WHERE users.school_id = :school_id AND users.id = :id AND translation_user_type.language_id = :language_id", $this->_user->school_id, $id, TranslationHandler::get_current_language());
            }
            else
            {
                $user_data = DbHandler::get_instance()->return_query("SELECT users.*, image.filename as profile_image, translation_user_type.title as user_type_title FROM users LEFT JOIN image ON image.id = users.image_id INNER JOIN translation_user_type ON translation_user_type.user_type_id = users.user_type_id WHERE users.id = :id AND translation_user_type.language_id = :language_id", $id, TranslationHandler::get_current_language());
            }

            if(isset($user_data) && !empty($user_data))
            {
                $this->temp_user = new User(reset($user_data));
            }
            else
            {
                throw new Exception("USER_DOESNT_EXIST");
            }
        }
        catch (Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
        return true;
    }

    public function import_users($csv_file, $school_id, $class_ids)
    {
        $this->import_has_add_info = false;
        $uploaded_file = "";
        $dir = '../../temp_files/';
        $file_opened = false;

        try
        {

            $this->validate_user_logged_in();

            if(!RightsHandler::has_user_right("ACCOUNT_CREATE"))
            {
                    throw new Exception("INSUFFICIENT_RIGHTS");
            }

            if(empty($csv_file['tmp_name']))
            {
                throw new Exception("IMPORT_NO_FILE");
            }

            if($this->_user->user_type_id != 1)
            {
                $school_id = $this->_user->school_id;
            }

            $users = array();
            $offset = 0;
            $index = 0;

            $this->check_if_csv($csv_file);
            $uploaded_file = $dir . $this->upload_csv($csv_file, $dir);

            $file = fopen($uploaded_file,"r");
            $file_opened = true;
            $fp = file($uploaded_file, FILE_SKIP_EMPTY_LINES);
            $count = count($fp);
            
            while(!feof($file))
            {

                $row = utf8_encode(fgets($file));
                $this->import_add_info = $index+1;
                if($index<$count)
                {
                    if($index > 0)
                    {
                        $users[] = $this->validate_csv_content($this->row_to_array($row), $offset, $school_id, $class_ids);
                    }
                }
                $index++;
            }

            $this->insert_csv_content($users);
            
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
        finally
        {
            if($file_opened)
            {
                fclose($file);
            }

            if(file_exists($uploaded_file))
            {
                unlink($uploaded_file);
            }
        }
    }

    private function upload_csv($file, $directory)
    {
        $new_file_name = $this->_user->username . "_" . date("Y-m-d_H-i-s") . ".csv";

        if(move_uploaded_file($file['tmp_name'], $directory . $new_file_name))
        {
            return $new_file_name;
        }

        throw new Exception("IMPORT_FAILED_UPLOAD");
    }

    private function insert_csv_content($users)
    {
        $this->users = array();

        foreach ($users as $user) 
        {
            if(empty($user->unhashed_password))
            {
                $this->create_user($user, true);
            }
            else
            {
                $this->create_user_with_password($user, true);
            }
        }
    }

    private function validate_csv_content($row, $offset, $school_id, $class_ids)
    {
        
        if(empty($row[0+$offset]) || empty($row[1+$offset]) || empty($row[2+$offset]))
        {
            $this->import_has_add_info = true;
            throw new Exception("IMPORT_MISSING_VALUE");
        }

        $user = new User();

        $firstname = $row[0+$offset];
        $surname = $row[1+$offset];
        $type = $row[2+$offset];
        $email = $row[3+$offset];
        $password = trim($row[4+$offset]);
        
        $this->check_if_valid_string($firstname, true);
        $this->check_if_valid_string($surname, true);
        $this->verify_class_ids($class_ids);
        
        $user->user_type_id = $this->check_if_valid_type($type);
        $user->firstname = $firstname;
        $user->surname = $surname;
        $user->class_ids = $class_ids;

        if(!empty($email))
        {
            $this->check_if_email($email, true);
            $this->mail_exists($email, true);
            $user->email = $email;
        }

        if(!empty($password))
        {
            if(strlen($password) < 6)
            {
                $this->import_has_add_info = true;
                throw new Exception("IMPORT_INVALID_PASSWORD");
            }
            $user->unhashed_password = $password;
        }

        $user->school_id = $school_id;
        
        return $user;
    }

    private function validate_csv_columns($row)
    {
        $count = count($row);
        $offset = 0;

        if($count != 5)
        {
            if($count > 5)
            {
                $offset = $count - 5;
            }
            else
            {
                throw new Exception("IMPORT_INVALID_FORMATTING");
            }
        }

        if($row[0+$offset] != "FIRST NAME" || $row[1+$offset] != "SURNAME"
        || $row[2+$offset] != "USER TYPE" || $row[3+$offset] != "EMAIL"
        || $row[4+$offset] != "PASSWORD")
        {
            throw new Exception("IMPORT_INVALID_FORMATTING");
        }
        return $offset;
    }

    private function check_if_csv($file)
    {
        $info = pathinfo($file['name']);
        if($info['extension']!="csv")
        {
            throw new Exception("IMPORT_INVALID_FORMAT");
        }
    }
    
    private function row_to_array($row_to_convert)
    {
        return explode(";", $row_to_convert);
    }

    private function check_if_valid_type($type)
    {
        $type = strtoupper($type);

        if(($type == "SA" && !RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN"))
            || ($type == "A" && !RightsHandler::has_user_right("ACCOUNT_CREATE_LOCADMIN")))
        {
            $this->import_has_add_info = true;
            throw new Exception("INSUFFICIENT_IMPORT_RIGHTS");
        }

        switch($type)
        {
            case "SA":
                return 1;
            case "A":
                return 2;
            case "T":
                return 3;
            case "S":
                return 4;
            default:
                $this->import_has_add_info = true;
                throw new Exception("IMPORT_INVALID_TYPE");
        }
    }

    public function get_profile_images()
    {
        try
        {
            $this->validate_user_logged_in();
            $this->profile_images = DbHandler::get_instance()->return_query("SELECT * FROM image");
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function mail_exists($email, $is_import = false)
    {
        $count = DbHandler::get_instance()->count_query("SELECT id FROM users WHERE email = :email", $email);

        if($count > 0)
        {
            if($is_import)
            {
                $this->import_has_add_info = true;
                throw new Exception("IMPORT_EMAIL_USED");
            }
            else
            {
                throw new Exception("CREATE_EMAIL_USED");
            }
        }
    }

    private function validate_user_logged_in()
    {
        if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
    }

    
}
?>
