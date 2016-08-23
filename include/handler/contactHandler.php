<?php

class ContactHandler extends Handler
{
    private $_support_email = "support@learnez.dk";

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
    
    public function generate_support_mail($name, $email, $context, $user_subject, $user_message)
    {
        try
        {
            $this->validate_mail_params($name, $email, $context, $user_subject, $user_message);
            
            $subject = $this->num_to_category($context) . " - " . htmlspecialchars($user_subject);
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . htmlspecialchars($email);

            $content = 
            '<html>
                <body>
                    <p><b>' . htmlspecialchars($name) . ", " . htmlspecialchars($email) . '</b></p>
                    <p><b>' . $subject . '</b></p>
                    <div>' . htmlspecialchars($user_message) . '</div>
                    <div></div></br>
                </body>
            </html>';

            mail($this->_support_email,$subject,$content,$headers);
            $this->generate_receipt_mail($email, $content);
            
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
    }
}

?>
