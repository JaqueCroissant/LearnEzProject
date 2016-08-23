<?php

class ContactHandler extends Handler
{
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
    
    public function generate_support_mail()
    {
        
    }
    
    private function generate_receipt_mail()
    {
        
    }
}

?>
