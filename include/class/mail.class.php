<?php

class Mail extends ORM {
    
    public $id;
    public $folder_id;
    public $folder_name;
    public $is_read;
    
    public $date;
    public $title;
    public $text;
    
    public $firstname;
    public $surname;
    public $sender_id;
    public $receiver_id;
}