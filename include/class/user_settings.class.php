<?php
    class User_Settings extends ORM
    {
        public $id;
        public $user_id;
        public $language_id;
        public $os_id;
        public $elements_shown;
        public $block_mail_notifications;
        public $block_student_mails;
        public $hide_profile;
        public $blocked_students;
        public $blocked_students_array = array();
        public $course_show_order;
        
        public function __construct() {
            if(func_num_args() != 1) {
                return;
            }

            if(!is_array(func_get_args()[0])) {
                return;
            }
            parent::__construct(func_get_args()[0]);

            if(empty($this->blocked_students)) {
                return;
            }
            try {
            $json = json_decode($this->blocked_students);
            if (json_last_error() == JSON_ERROR_NONE || json_last_error() == 0) {
                $users = DbHandler::get_instance()->return_query("SELECT id, firstname, surname FROM users WHERE id IN (".generate_in_query($json).") AND user_type_id = '4'");
                
                if(empty($users)) {
                    $this->blocked_students = array();
                    return;
                }
                
                $array = array();
                foreach($users as $value) {
                    $array[] = new User($value);
                    $this->blocked_students_array[$value["id"]] = $value["id"];
                }
                $this->blocked_students = $array;
                return;
            }
            $this->blocked_students = array();
            } catch(Exception $ex) {
            }
        }
    }
?>
