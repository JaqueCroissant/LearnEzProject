<?php
class MailHandler extends Handler
{
    private $_current_folder_id;
    
    public $folders = array();
    public $mails = array();
    
    public function __construct() {
        parent::__construct();
    }
    
    private function is_sender_folder($folder_id = 0) {
        return ($folder_id > 0 ? $folder_id : $this->_current_folder_id) == 3;
    }
    
    private function get_folder_id($folder_prefix = 'inbox') {
        switch($folder_prefix) {
            default: return 1; case 'important': return 2; case 'sent': return 3; 
            case 'drafts': return 4; case 'spam': return 5; case 'trash': return 6;
        }
    }
    
    public function get_folders() {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT mail_folder.id, mail_folder.folder_name, mail_folder.icon_class, translation_mail_folder.title FROM mail_folder INNER JOIN translation_mail_folder ON translation_mail_folder.mail_folder_id = mail_folder.id WHERE translation_mail_folder.language_id = :language_id", TranslationHandler::get_current_language());
            
            if(empty($data) || !is_array($data) || count($data) < 1) {
                throw new exception("NO_MAIL_FOLDERS_FOUND");
            }
            
            foreach($data as $key => $value) {
                $this->folders[$key] = new MailFolder($value);
            }
            
            return true;
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }

    public function get_mails($folder_prefix = null) {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            $this->_current_folder_id = $this->get_folder_id($folder_prefix);
            
            $query = "SELECT mail.id, mail.date, mail.title, mail.text, mail_folder.id as folder_id, mail_folder.folder_name, user_mail.receiver_id, user_mail.sender_id"
                    . " FROM mail INNER JOIN user_mail ON user_mail.mail_id = mail.id"
                    . " INNER JOIN mail_folder ON mail_folder.id = user_mail.". ($this->is_sender_folder() ? "sender_folder_id" : "receiver_folder_id")
                    . " WHERE user_mail.". ($this->is_sender_folder() ? "sender_id" : "receiver_id") ." = :user_id ".(!$this->is_sender_folder() ? "AND user_mail.receiver_folder_id = :folder_id" : "");
            
            $data = $this->is_sender_folder() ? DbHandler::get_instance()->return_query($query, $this->_user->id) : DbHandler::get_instance()->return_query($query, $this->_user->id, $this->_current_folder_id);
            
            if(empty($data) || !is_array($data) || count($data) < 1) {
                return true;
            }
            
            foreach($data as $value) {
                $this->mails[] = new Mail($value);
            }
            
            return true;
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }
}