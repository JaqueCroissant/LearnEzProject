<?php

class ContactHandler extends Handler
{
    //private $_support_email = "support@learnez.dk";
    private $_support_email = "sunfolk88@gmail.com";

    public function __construct()
    {
        parent::__construct();
    }
    
    public function is_logged_in()
    {
        return parent::user_exists();
    }
    
    public function generate_reset_mail($user_id, $email, $validation_code)
    {
        try
        {
            $url = "http://project.learnez.dk?page=resetpassword&step=confirmpassword&id=" . $user_id . "&code=" . $validation_code;
        
            $subject = TranslationHandler::get_static_text("RESET_PASS_MAIL_SUBJECT");
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: no-reply@learnez.dk";
            $message = TranslationHandler::get_static_text("RESET_PASS_MAIL_MESSAGE");
            $period_pos = strpos($message, ".");
            $comma_pos = strpos($message, ",");

            $content = 
            '<html>
                <head>
                </head>

                <body>
                    <p>' . TranslationHandler::get_static_text("HELLO") . "!" . '</p>
                    <p>' . substr($message, 0, $period_pos) . '</p>
                    <div>' . substr($message, $period_pos + 2, $comma_pos - $period_pos)  . '</div>
                    <div>' . substr($message, $comma_pos + 2, strlen($message) - $comma_pos + 2)  . '</div></br>
                    <p><a href="'. $url .'">' . TranslationHandler::get_static_text("RESET_PASS_MAIL_LINK_MESSAGE") . 
                    '</a></p>

                    <p>LearnEZ</p>
                </body>
            </html>';

            mail($email,$subject,$content,$headers);
            
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
        
    }
    
    public function generate_support_mail($name, $email, $context, $user_subject, $user_message, $is_logged_in = false)
    {
        try
        {
            $this->validate_mail_params($name, $email, $context, $user_subject, $user_message);
            $user_affiliations = $this->get_user_affiliations($email, $is_logged_in);
            
            $subject = $this->num_to_category($context) . " - " . htmlspecialchars($user_subject);
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . htmlspecialchars($email);

            $content = 
            '<html>
                <body>
                    <p><b>' . htmlspecialchars($name) . $user_affiliations['user_type'] . ", " . htmlspecialchars($email) . '</b></p>' .
                    $user_affiliations['school'] . $user_affiliations['classes'] .
                    '<p><b>' . $subject . '</b></p>
                    <div>' . htmlspecialchars($user_message) . '</div>
                    <div></div></br>
                </body>
            </html>';

            mail($this->_support_email,$subject,$content,$headers);
            $this->generate_receipt_mail($email, $content);
            $this->log_activity($email);
            
            return true;
        }
        catch(Exception $ex)
        {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    private function generate_receipt_mail($email, $content)
    {
        $message = TranslationHandler::get_static_text("SUPPORT_RECEIPT");
        $subject = TranslationHandler::get_static_text("SUPPORT_RECEIPT_SUBJECT");
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@learnez.dk";
        
        $period_pos = strpos($message, ".");
        $comma_pos = strpos($message, "!");
        
        $receipt = '<p>' . TranslationHandler::get_static_text("HELLO") . "!" . '</p>
                    <p>' . substr($message, 0, $period_pos) . '</p>
                    <div>' . substr($message, $period_pos + 2, $comma_pos - $period_pos)  . '</div>
                    <div>' . substr($message, $comma_pos + 2, strlen($message) - $comma_pos + 2)  . '</div></br>'
                    . $content;
        
        mail($email,$subject,$receipt,$headers);
    }
    
    private function num_to_category($num)
    {
        switch($num)
        {
            case 1:
                return "TECHNICAL";
            case 2:
                return "ACCOUNT";
            case 3:
                return "OTHER";
            default:
                return "HACK";
        }
    }
    
    private function validate_mail_params($name, $email, $context, $subject, $message)
    {
        if(empty($name) || empty($email) || empty($context) || empty($subject) || empty($message))
        {
            throw new Exception("USER_EMPTY_FORM");
        }
        
        if(!is_string($name) || !is_string($subject) || !is_string($message))
        {
            throw new Exception("INVALID_INPUT");
        }
        
        if(!is_numeric($context))
        {
            throw new Exception("INVALID_INPUT");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new Exception("EMAIL_HAS_WRONG_FORMAT");
        }
        /*
        if(isset($_COOKIE['zk4b']) && strtotime($_COOKIE['zk4b']))
        {
            if(strtotime($_COOKIE['zk4b']) > strtotime("-15 minutes"))
            {
                throw new Exception("CONTACT_INVALID_TIME");
            }
        }
        else
        {
            $this->validate_timeframe($email);
        }
        */
    }
    
    private function log_activity($email)
    {
        $_COOKIE['zk4b'] = date("Y-m-d H:i:s");
        DbHandler::get_instance()->query("INSERT INTO contact_record (time, email) VALUES (NOW(), :email)", $email);
    }
    
    private function validate_timeframe($email)
    {
        if(DbHandler::get_instance()->count_query("SELECT id FROM contact_record WHERE time > DATE_SUB(NOW(), INTERVAL 15 MINUTE) AND email = :email", $email) > 0)
        {
            throw new Exception("CONTACT_INVALID_TIME");
        }
    }
    
    private function get_user_affiliations($email, $is_logged_in)
    {
        $temp_data = array();
        
        if($is_logged_in)
        {
            $data = DbHandler::get_instance()->return_query("SELECT school.name, school.city, class.title "
                    . "FROM school LEFT JOIN user_class ON user_class.users_id = :user_id "
                    . "LEFT JOIN class ON class.id = user_class.class_id WHERE school.id = :school_id", 
                    $this->_user->id, $this->_user->school_id);
            
            if(count($data) > 0)
            {
                $return_data['school_name'] = $data[0]['name'];
                $return_data['school_city'] = $data[0]['city'];
                $return_data['classes'] = array();
                foreach($data as $value)
                {
                    $temp_data['classes'][] = $value['title'];
                }
            }
            
            $school = (isset($return_data['school_name']) && isset($return_data['school_city'])) ? "<p><b>School: " . $return_data['school_name'] . ", " . $return_data['school_city'] . '</b></p>' : "";
            $classes = "";
            
            if(!empty($school) && isset($temp_data['classes']))
            {
                $classes = "<p><b>Classes: ";
                for($i = 0; $i < count($temp_data['classes']); $i++)
                {
                    $classes .= $temp_data['classes'][$i];
                    
                    if($i != count($temp_data['classes'])-1)
                    {
                        $classes .= ", ";
                    }
                }
                $classes .= "</b></p>";
            }
            
            $return_data['user_type'] = ", " . $this->_user->user_type_title;
            $return_data['school'] = $school;
            $return_data['classes'] = $classes;
        }
        else
        {
            if(DbHandler::get_instance()->count_query("SELECT id FROM users WHERE email = :email", $email) < 1)
            {
                $return_data['user_type'] = "";
                $return_data['school'] = "";
                $return_data['classes'] = "";
                return $return_data;
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT translation_user_type.title AS user_type, "
                    . "school.name, school.city, class.title FROM translation_user_type "
                    . "INNER JOIN users ON users.user_type_id = translation_user_type.user_type_id "
                    . "AND users.email = :email LEFT JOIN school ON school.id = users.school_id "
                    . "LEFT JOIN user_class ON user_class.users_id = users.id "
                    . "LEFT JOIN class ON class.id = user_class.class_id WHERE translation_user_type.language_id = :language", 
                    $email, 2);
            
            if(count($data) > 0)
            {
                $return_data['school_name'] = $data[0]['name'];
                $return_data['school_city'] = $data[0]['city'];
                $return_data['user_type'] = ", " . $data[0]['user_type'];
                $return_data['classes'] = array();
                foreach($data as $value)
                {
                    $temp_data['classes'][] = $value['title'];
                }
            }
            
            $school = (isset($return_data['school_name']) && isset($return_data['school_city'])) ? "<p><b>School: " . $return_data['school_name'] . ", " . $return_data['school_city'] . '</b></p>' : "";
            $classes = "";
            
            if(!empty($school) && isset($temp_data['classes']))
            {
                $classes = "<p><b>Classes: ";
                for($i = 0; $i < count($temp_data['classes']); $i++)
                {
                    $classes .= $temp_data['classes'][$i];
                    
                    if($i != count($temp_data['classes'])-1)
                    {
                        $classes .= ", ";
                    }
                }
                $classes .= "</b></p>";
            }
            
            $return_data['school'] = $school;
            $return_data['classes'] = $classes;
        }
        return $return_data;
    }
}

?>
