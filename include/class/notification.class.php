<?php

class Notification extends ORM
    {
        public $id;
        public $title;
        public $text;
        public $language_id;
        public $datetime;
        public $isRead;
        public $sender_name;
    }

