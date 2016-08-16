<?php

class MediaHandler extends Handler {

    public $file_name;
    public $compressed_file_type;
    public $file_duration;
    
    private $current_folder;
    private $temporary_folder;
    
    private $base_folder;
    private $copy_file;
    private $compressed_file_path;
    
    public function __construct() {
        $this->copy_file = realpath(__DIR__ . '/../..') . "/courses/core/copy/index.php";
    }
    
    public function delete($path = null) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            
            $base_path = realpath(__DIR__ . '/../..') . "/courses/";
            
            if(empty($path) || $path == "lectures/" || $path == "tests/" || !file_exists($base_path . $path)) {
                throw new exception("INVALID_INPUT");
            }
            
            $this->delete_directory($base_path . $path);
            
            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }

    public function upload_test($file = null) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if (!array_key_exists("size", $file) || empty($file["size"])) {
                throw new exception("INVALID_INPUT");
            }

            if ($file["size"] > 500000000) {
                throw new exception("FILE_TOO_LARGE_500");
            }

            $this->compressed_file_type = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!in_array(strtoupper($this->compressed_file_type), array("ZIP"))) {
                throw new exception("INVALID_FILE_TYPE_TEST");
            }

            $this->upload_compressed_file($file);

            $this->extract_file_content();
            
            $this->extraction_clean_up();

            return true;
        } catch (Exception $ex) {
            $this->file_clean_up();
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
     public function upload_lecture($file = null) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }

            if (!RightsHandler::has_user_right("COURSE_ADMINISTRATE")) {
                throw new exception("INSUFFICIENT_RIGHTS");
            }

            if (!array_key_exists("size", $file) || empty($file["size"])) {
                throw new exception("INVALID_INPUT");
            }

            if ($file["size"] > 500000000) {
                throw new exception("FILE_TOO_LARGE_500");
            }

            $this->compressed_file_type = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!in_array(strtoupper($this->compressed_file_type), array("MP4"))) {
                throw new exception("INVALID_FILE_TYPE_LECTURE");
            }

            $this->upload_file($file);

            return true;
        } catch (Exception $ex) {
            //echo $ex->getMessage();
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    private function upload_file($file) {
        $this->base_folder = realpath(__DIR__ . '/../..') . "/courses/lectures";

        while (true) {
            $file_name = md5(uniqid(mt_rand(), true));

            if (!file_exists($this->base_folder . "/" . $file_name)) {
                $this->file_name = $file_name;
                break;
            }
        }
        $this->compressed_file_path = $this->base_folder . "/" . $this->file_name . "." . $this->compressed_file_type;
        if (!move_uploaded_file($file["tmp_name"], $this->compressed_file_path)) {
            throw new exception("UNKNOWN_ERROR");
        }
        
        $this->file_duration = floor(MP4Info::getInfo($this->compressed_file_path)->duration);
    }

    private function upload_compressed_file($file) {
        $this->base_folder = realpath(__DIR__ . '/../..') . "/courses/tests";

        while (true) {
            $file_name = md5(uniqid(mt_rand(), true));

            if (!file_exists($this->base_folder . "/" . $file_name)) {
                $this->file_name = $file_name;
                break;
            }
        }
        $this->compressed_file_path = $this->base_folder . "/raw/" . $this->file_name . "." . $this->compressed_file_type;
        if (!move_uploaded_file($file["tmp_name"], $this->compressed_file_path)) {
            throw new exception("UNKNOWN_ERROR");
        }
    }

    private function extract_file_content() {
        $this->current_folder = $this->base_folder . "/" . $this->file_name;
        $this->temporary_folder = $this->base_folder . "/raw/" . $this->file_name;
        mkdir($this->temporary_folder, 0777, true);

        $zipArchive = new ZipArchive;
        $current_file = $zipArchive->open($this->compressed_file_path);

        if (!$current_file) {
            throw new exception("EXTRACTION_FAILED");
        }

        $zipArchive->extractTo($this->temporary_folder);
        $zipArchive->close();
        $this->rename_base_folder();

        unlink($this->compressed_file_path);
    }

    private function file_clean_up() {
        if (file_exists($this->compressed_file_path)) {
            unlink($this->compressed_file_path);
        }
        
        if (file_exists($this->temporary_folder)) {
            $this->delete_directory($this->temporary_folder);
        }

        if (file_exists($this->current_folder)) {
            $this->delete_directory($this->current_folder);
        }
    }

    private function rename_base_folder() {
        if (!is_dir($this->temporary_folder)) {
            return;
        }
        $files = glob($this->temporary_folder . '/*');
        
        foreach ($files as $file) {
            if (is_dir($file)) {
                rename($file, $this->current_folder);
                $this->delete_directory($this->temporary_folder);
                break;
            }
            throw new exception("INVALID_UPLOAD_STRUCTURE");
        }
    }
    
    private function extraction_clean_up() {
        $files = glob($this->current_folder . '/*.*');
        
        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }
            
            $values = explode("/", $file);
            $last_element = array_pop($values);
            if($last_element == "index.html") {
                unlink($file);
            }
            
            $file_info = explode(".", $last_element);
            
            if(array_key_exists(1, $file_info)) {
                switch($file_info[1]) {
                    case "swf":
                        unlink($file);
                        break;
                }
            }
        }
        
        copy($this->copy_file, $this->current_folder . "/index.php");
    }

    private function delete_directory($path) {
        if (!is_dir($path)) {
            if(!file_exists($path)) {
                throw new exception("INVALID_INPUT");
            }
            unlink($path);
            return;
        }

        if (substr($path, strlen($path) - 1, 1) != '/') {
            $path .= '/';
        }

        $files = glob($path . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->delete_directory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }
    
    public static function file_exists($file_name, $type = "lectures") {
        switch($type) {
            case "test":
                return file_exists(realpath(__DIR__ . '/../..') . "/courses/tests/". $file_name);
            
            default:
                return file_exists(realpath(__DIR__ . '/../..') . "/courses/lectures/". $file_name);
        }
    }

}
