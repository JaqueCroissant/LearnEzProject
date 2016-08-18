<?php
class ErrorHandler
{
    public static $error;

    public static function return_error($errorCode = null)
    {
        self::$error = self::set_error_messages($errorCode);
        return self::$error;
    }

    private static function set_error_messages($errorCode = null)
    {
        $error = DbHandler::get_instance()->return_query("SELECT error.prefix, translation_error.title, translation_error.text FROM error INNER JOIN translation_error ON translation_error.error_id = error.id WHERE error.prefix = :prefix AND translation_error.language_id = :language_id", $errorCode, TranslationHandler::get_current_language());
        if($error != null) {
            return new Error(reset($error)["title"], reset($error)["text"], $errorCode);
        }
        return new Error("Unknown error occoured.", "Unknown error occoured.", $errorCode);
    }
    
    public static function show_error_page($error = null) {
        $_SESSION["temp_error"] = is_a($error, "Error") ? $error->title : $error;
        echo "<script>change_page('error');</script>";
    }
}
?>