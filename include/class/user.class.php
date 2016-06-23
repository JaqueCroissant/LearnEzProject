<?php
    class User
    {
        public $id;
        public $language_id;
        public $user_type_id;
        public $last_login;

        public $username;
        public $firstname;
        public $surname;
        public $email;
        
        private $data_array;
        
        public function __construct($user_info) {
            if(!is_array($user_info)) {
                return;
            }

            $this->data_array = $user_info;
            $this->iterate_properties();
            $this->data_array = null;
        }
        
        private function iterate_properties() {
            foreach(get_object_vars($this) as $key => $value) {
                if(array_key_exists($key, $this->data_array)) {
                    $this->$key = $this->data_array[$key];
                }
            }
        }
        
    }
?>