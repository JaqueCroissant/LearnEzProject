<?php

class Mail extends ORM {
    
    public $id;
    public $user_mail_id;
    public $folder_id;
    public $folder_name;
    public $is_read;
    public $disable_reply;
    
    public $date;
    public $title;
    public $text;
    public $mail_tags = array();
    
    public $firstname;
    public $surname;
    public $user_image_id;
    public $sender_id;
    public $receiver_id;
    
    public function __construct() {
        
        if(func_num_args() != 1) {
            return;
        }
        
        if(!is_array(func_get_args()[0])) {
            return;
        }
        parent::__construct(func_get_args()[0]);
        
        if(empty($this->id)) {
            return;
        }
        
        $data = DbHandler::get_instance()->return_query("SELECT mail_tags.id, mail_tags.color_class, translation_mail_tags.title FROM user_mail_tags"
                . " INNER JOIN mail_tags ON mail_tags.id = user_mail_tags.mail_tag_id"
                . " INNER JOIN translation_mail_tags ON translation_mail_tags.mail_tag_id = mail_tags.id"
                . " WHERE user_mail_tags.mail_id = :mail_id AND translation_mail_tags.language_id = :language_id", $this->id, TranslationHandler::get_current_language());
        
        if(empty($data) || !is_array($data) || count($data) < 1) {
            return;
        }
        
        foreach($data as $value) {
            $this->mail_tags[] = new MailTag($value);
        }
        
    }
}