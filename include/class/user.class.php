<?php
    class User extends ORM
    {
        public $id;
        public $user_type_id;
        public $last_login;
        public $school_id;
        public $class_ids;
        public $image_id;
        public $profile_image;
        public $image_name;
        public $time_created;
        public $open;

        public $username;
        public $firstname;
        public $surname;
        public $email;
        public $description;
        public $school_name;
        public $user_type;
        public $user_type_title;
        public $points;
        public $validation_code;
        public $last_password_request;
        public $unhashed_password;
        
        public $settings;
        public $homework_complete;
        public $lectures = array();
        public $tests = array();
        
        public function __construct() {
            
            if(func_num_args() != 1 && func_num_args() != 2) {
                return;
            }

            if(!is_array(func_get_args()[0])) {
                return;
            }
            parent::__construct(func_get_args()[0]);

            if(func_num_args() == 1) {
                return;
            }
            
            if(!is_bool(func_get_args()[1]) || !func_get_args()[1]) {
                return;
            }
            
            if(empty($this->id)) {
                return;
            }

            $data = DbHandler::get_instance()->return_query("SELECT * FROM user_settings WHERE user_id = :user_id LIMIT 1", $this->id);

            if(empty($data) || !is_array($data) || count($data) < 1) {
                return;
            }
            
            $this->settings = new User_Settings(reset($data));
        }
    }
?>