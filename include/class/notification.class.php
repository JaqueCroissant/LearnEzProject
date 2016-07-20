<?php

class Notification extends ORM
    {
        public $id;
        public $title;
        public $text;
        public $language_id;
        public $datetime;
        public $isRead;
        public $category;
        public $icon;
        public $link_page;
        public $link_step;
        public $link_args;
        public $arg_id;
    }

