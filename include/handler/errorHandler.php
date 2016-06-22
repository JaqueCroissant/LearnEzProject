<?php
class ErrorHandler
{
    public static $error;

    public static function ReturnError($errorCode = null)
    {
        self::$error = self::SetErrorMessage($errorCode);
        return self::$error;
    }

    private static function SetErrorMessage($errorCode = null)
    {
        $userData = DbHandler::getInstance()->CountQuery("SELECT error.prefix, translation_error.title, translation_error.text FROM error INNER JOIN translation_error ON translation_error.error_id = error.id WHERE translation_error.language_id = :language_id", 1);
        $errorTitle = null;
        $errorText = null;
        switch ($errorCode)
        {
            case "DATABASE_COULD_NOT_CONNECT":
                $errorTitle = "Could not connect to the database.";
                $errorText = "Could not connect to the database.";
                break;
                
            case "LOGIN_INVALID_FORM":
                $errorTitle = "Invalid login form.";
                $errorText = "Invalid login form.";
                break;
                
            case "LOGIN_EMPTY_FORM":
                $errorTitle = "You must fill out the username and password.";
                $errorText = "You must fill out the username and password.";
                break;
                
            case "LOGIN_ALREADY_EXISTS":
                $errorTitle = "You are already logged in.";
                $errorText = "You are already logged in.";
                break;
                
            case "LOGIN_INVALID_USERNAME":
                $errorTitle = "Invalid username.";
                $errorText = "Invalid username.";
                break;
                
            case "LOGIN_INVALID_PASSWORD":
                $errorTitle = "Invalid password.";
                $errorText = "Invalid password.";
                break;
            
            default:
                $errorTitle = "An unknown error occoured";
                $errorText = "An unknown error occoured";
                break;
        }
        return new Error($errorTitle, $errorText);
    }
}
?>