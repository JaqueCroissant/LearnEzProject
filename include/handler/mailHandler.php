<?php
class MailHandler extends Handler
{
    private $_current_folder_id;

    public $mails_removed;
    public $current_folder;
    public $current_mail;
    public $folders = array();
    public $mails = array();
    public $tags = array();
    
    public function __construct($current_page = null) {
        parent::__construct();
        $this->initialize_folders($current_page);
        $this->get_tags();
    }
    
    private function get_tags() {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT mail_tags.id, translation_mail_tags.title FROM mail_tags INNER JOIN translation_mail_tags ON translation_mail_tags.mail_tag_id = mail_tags.id  WHERE translation_mail_tags.language_id = :language_id", TranslationHandler::get_current_language());
            
            if(empty($data) || !is_array($data) || count($data) < 1) {
                throw new exception();
            }
            
            foreach($data as $key => $value) {
                $this->tags[] = new MailTag($value);
            }
            return true;
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }
    
    private function initialize_folders($current_page = null) {
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
                $this->folders[$value["folder_name"]] = new MailFolder($value);
            }
            $this->current_folder = array_key_exists($current_page, $this->folders) ? $this->folders[$current_page] : array_shift(array_values($this->folders)); ;
            
            return true;
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }
    
    public function get_mail($mail_id = 0) {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            if (empty($mail_id) || !is_numeric($mail_id)) {
                throw new exception("MAIL_INVALID_INPUT");
            }
            
            
            $query = "SELECT mail.id, mail.date, mail.title, mail.text, user_mail.receiver_id, user_mail.sender_id, user_mail.receiver_folder_id, user_mail.sender_folder_id"
                    . " FROM mail INNER JOIN user_mail ON user_mail.mail_id = mail.id"
                    . " WHERE mail.id = :mail_id AND (user_mail.receiver_id = :user_id OR user_mail.sender_id = :user_id) LIMIT 1";
            
            $data = reset(DbHandler::get_instance()->return_query($query, $mail_id, $this->_user->id, $this->_user->id));
            
            if(empty($data) || !is_array($data) || count($data) < 1) {
                throw new exception("MAIL_INVALID_MAIL_ID");
            }
            
            if(!empty($data["receiver_id"]) && $data["receiver_id"] == $this->_user->id && empty($data["receiver_folder_id"])) {
                throw new exception("MAIL_INVALID_MAIL_ID");
            }
            
            if(!empty($data["sender_id"]) && $data["sender_id"] == $this->_user->id && (empty($data["sender_folder_id"]) || $data["sender_folder_id"] != "3")) {
                throw new exception("MAIL_INVALID_MAIL_ID");
            }
            
            $this->_current_folder_id = (!empty($data["sender_id"]) && $data["sender_id"] == $this->_user->id ? 3 : $data["receiver_folder_id"]);
            
            if($this->_current_folder_id < 1 || $this->_current_folder_id == 4 || $this->_current_folder_id > 6) {
                throw new exception("MAIL_INVALID_MAIL_ID");
            }
            
            $query = "SELECT mail_folder.id as folder_id, mail_folder.folder_name, users.firstname, users.surname FROM user_mail
                INNER JOIN mail_folder ON mail_folder.id = user_mail.". ($this->is_sender_folder() ? "sender_folder_id" : "receiver_folder_id") ."
                INNER JOIN users ON users.id = user_mail.". ($this->is_sender_folder() ? "receiver_id" : "sender_id") ."
                WHERE user_mail.mail_id = :mail_id AND user_mail.". ($this->is_sender_folder() ? "sender_id" : "receiver_id") ." = :user_id LIMIT 1";
            
            $new_data = reset(DbHandler::get_instance()->return_query($query, $mail_id, $this->_user->id));
            
            if(empty($new_data) || !is_array($new_data) || count($new_data) < 1) {
                throw new exception("MAIL_INVALID_MAIL_ID");
            }
            
            $this->current_mail = new Mail(array_merge($data, $new_data));
            return true;
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }

    public function get_mails($current_page_number = 0, $order_ascending = 0, $read_unread_all = 0) {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            if (!is_numeric($order_ascending) || !is_numeric($read_unread_all)) {
                throw new exception();
            }

            $this->_current_folder_id = $this->get_folder_id($this->current_folder->folder_name);
            
            $query = "SELECT mail.id, mail.date, mail.title, mail.text, mail_folder.id as folder_id, mail_folder.folder_name, user_mail.receiver_id, user_mail.sender_id, user_mail.is_read, users.firstname, users.surname"
                    . " FROM mail INNER JOIN user_mail ON user_mail.mail_id = mail.id"
                    . " INNER JOIN users ON users.id = ". ($this->is_sender_folder() ? "receiver_id" : "sender_id")
                    . " INNER JOIN mail_folder ON mail_folder.id = user_mail.". ($this->is_sender_folder() ? "sender_folder_id" : "receiver_folder_id")
                    . " WHERE user_mail.". ($this->is_sender_folder() ? "sender_id" : "receiver_id") ." = :user_id ".(!$this->is_sender_folder() ? "AND user_mail.receiver_folder_id = :folder_id" : "");
            
            $query .= !$this->is_sender_folder() ? ($read_unread_all == 1 ? " AND user_mail.is_read is false" : ($read_unread_all == 2 ? " AND user_mail.is_read is true" : "")) : "";
            $query .= $order_ascending == 1 ? " ORDER BY mail.date ASC" : " ORDER BY mail.date DESC";
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
    
    public function assign_mail_folder($folder_prefix = null, $mails = array()) {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            if (empty($folder_prefix)) {
                throw new exception("MAIL_INVALID_FOLDER");
            }
            
            if(empty($mails) || !is_array($mails) || count($mails) < 1) {
                throw new exception("MAIL_EMPTY_MAIL_ARRAY");
            }

            $folder_id = $this->get_folder_id($folder_prefix);
            
            if(!$this->check_folder_rights($this->current_folder->id, $folder_id)) {
                throw new exception("MAIL_INVALID_INPUT2");
            }

            switch($this->current_folder->id) {
                case 1: case 2: case 5:
                    $this->update_mail_folder($mails, $folder_id);
                    break;
                    
                case 6:
                    $this->update_mail_folder($mails, $folder_id);
                    break;
                
                case 3:
                    $this->update_mail_folder($mails, $folder_id, true);
                    break;
                    
                case 4:
                    $this->update_mail_folder($mails, $folder_id, true);
                    break;
            }
            $this->mails_removed = $mails;
            return true;
	}
	catch (Exception $ex) 
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }
    
    public function send_mail($title = null, $message = null, $recipiants = array(), $disable_reply = false, $tags = array()) {
        try
        {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            
            if (empty($title)) {
                throw new exception("MAIL_MUST_FILL_TITLE");
            }
            
            if (empty($message)) {
                throw new exception("MAIL_MUST_FILL_MESSAGE");
            }
            
            if(empty($recipiants) || !is_array($recipiants) || count($recipiants) < 1) {
                throw new exception("MAIL_MUST_FILL_RECIPIANTS");
            }
            
            foreach($recipiants as $value) {
                if(!is_numeric($value) || $value == $this->_user->id) {
                    throw new exception("MAIL_MUST_FILL_RECIPIANTS");
                }
            }
            
            DbHandler::get_instance()->query("INSERT INTO mail (date, title, text, disable_reply) VALUES (:date, :title, :text, :disable_reply)", date("Y-m-d H:i:s"), $title, $message, $disable_reply);
            $last_inserted_id = DbHandler::get_instance()->last_inserted_id();
            DbHandler::get_instance()->query("INSERT INTO user_mail (mail_id, sender_id, receiver_id, sender_folder_id, receiver_folder_id) VALUES (:last_inserted_id, :sender_id, :receiver_id, :sender_folder_id, :receiver_folder_id)", $last_inserted_id, $this->_user->id, reset($recipiants), 3, 1);
            
            if(is_array($tags) && count($tags) > 0) {
                $data = DbHandler::get_instance()->return_query("SELECT id FROM mail_tags");
                
                foreach($tags as $value) {
                    if(!is_numeric($value)) {
                        continue;
                    }
                    
                    $isset = false;
                    foreach($data as $data_value) {
                        if($data_value["id"] == $value) {
                            $isset = true;
                            break;
                        }
                    }
                    
                    if(!$isset) {
                       continue;
                    }
                    
                    DbHandler::get_instance()->query("INSERT INTO user_mail_tags (mail_id, mail_tag_id) VALUES (:mail_id, :mail_tag_id)", $last_inserted_id, $value);
                }
            }
            return true;
	}
	catch (Exception $ex) 
        {
            echo $ex->getMessage();
            $this->error = ErrorHandler::return_error($ex->getMessage());
	}
        return false;
    }
    
    private function update_mail_folder($mails, $folder_id, $sender = false) {
        $in_query = "";
        for($i = 0; $i < count($mails); $i++) {

            if(!is_numeric($mails[$i])) {
                throw new exception("MAIL_INVALID_INPUT");
            }

            $in_query .= ($i > 0 ? "," : "") ." '". $mails[$i] ."'";
        }
        
        $data = DbHandler::get_instance()->return_query("SELECT mail.id FROM mail INNER JOIN user_mail ON user_mail.mail_id = mail.id WHERE mail.id IN (". $in_query .") AND user_mail." . ($sender ? "sender_id" : "receiver_id") ." = :user_id", $this->_user->id);
                
        if(empty($data) || count($data) != count($mails)) {
            throw new exception("MAIL_INVALID_INPUT");
        }

        $in_query = "";
        for($i = 0; $i < count($data); $i++) {
            $in_query .= ($i > 0 ? "," : "") ." '". $data[$i]["id"] ."'";
        }
        //echo "UPDATE user_mail SET " . ($sender ? "sender_folder_id" : "receiver_folder_id") ." = :". $folder_id . " WHERE id IN (". $in_query .")";
        DbHandler::get_instance()->query("UPDATE user_mail SET " . ($sender ? "sender_folder_id" : "receiver_folder_id") ." = :folder_id WHERE mail_id IN (". $in_query .")", $folder_id);
                    
    }
    
    private function get_folder_id($folder_prefix = 'inbox') {
        switch($folder_prefix) {
            default: return 1; case 'important': return 2; case 'sent': return 3; 
            case 'drafts': return 4; case 'spam': return 5; case 'trash': return 6;
            case 'delete': return 7;
        }
    }
    
    private function check_folder_rights($folder_prefix, $target_id) {
        switch($folder_prefix) {
            case 1:
                if($target_id == 2 || $target_id == 5 || $target_id == 6) {
                    return true;
                }
                break;
                
            case 2:
                if($target_id == 1 || $target_id == 5 || $target_id == 6) {
                    return true;
                }
                break;
                
            case 3:
            case 4:
                if($target_id == 7) {
                    return true;
                }
                break;
                
            case 5:
                if($target_id == 1 || $target_id == 6) {
                    return true;
                }
                break;
                
            case 6:
                if($target_id == 1 || $target_id == 7) {
                    return true;
                }
                break;
        }
        return false;
    }
    
    private function is_sender_folder($folder_id = 0) {
        return ($folder_id > 0 ? $folder_id : $this->_current_folder_id) == 3;
    }
}