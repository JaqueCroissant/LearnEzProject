<?php
class ErrorHandler
{
    public static $Error;

    public static function ReturnError($errorCode = null)
    {
        ErrorHandler::$Error = ErrorHandler::SetErrorMessage($errorCode);
        return ErrorHandler::$Error;
    }

    private static function SetErrorMessage($errorCode = null)
    {
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