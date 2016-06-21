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
        $errorMessage;
        switch ($errorCode)
        {
            case "INVALID_FORM":
                $errorMessage = "You must use our webform.";
                break;
            
            default:
                $errorMessage = "An error occoured";
        }
        return $errorMessage;
    }
}
?>