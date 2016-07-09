<?php

class Mail extends ORM {
    
    public $id;
    public $folder_id;
    public $folder_name;
    
    public $date;
    public $title;
    public $text;
    
    public $sender_id;
    public $receiver_id;
}