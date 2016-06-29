<?php
    class User extends ORM
    {
        public $id;
        public $language_id;
        public $user_type_id;
        public $last_login;
        public $school_id;
        public $class_ids;
        public $image_id;
        public $time_created;

        public $username;
        public $firstname;
        public $surname;
        public $email;
        public $description;

        public $validation_code;
        public $last_password_request;
        public $unhashed_password;
    }
?>