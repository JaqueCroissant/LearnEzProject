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
        $error = DbHandler::getInstance()->ReturnQuery("SELECT error.prefix, translation_error.title, translation_error.text FROM error INNER JOIN translation_error ON translation_error.error_id = error.id WHERE error.prefix = :prefix AND translation_error.language_id = :language_id", $errorCode, 1);
        if($error != null) {
            return new Error(reset($error)["title"], reset($error)["text"]);
        }
        return new Error("Unknown error occoured.", "Unknown error occoured.");
    }
}
?>