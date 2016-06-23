<?php
    class User extends ORM
    {
        public $id;
        public $language_id;
        public $user_type_id;
        public $last_login;

        public $username;
        public $firstname;
        public $surname;
        public $email;
    }
?>