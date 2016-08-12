<?php 

class MediaHandler extends Handler {
    
    private $current_folder;
    private $base_folder;
    
    private $compressed_file_name;
    private $compressed_file_type;
    private $compressed_file_path;
    
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
            
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            $this->file_clean_up();
            $this->error = ErrorHandler::return_error($ex->getMessage());
        }
        return false;
    }
    
    private function upload_compressed_file($file) {
        $this->base_folder = realpath(__DIR__ . '/../..') . "/courses/tests";
        
        while(true) {
            $file_name = md5(uniqid(mt_rand(), true));
            
            if(!file_exists($this->base_folder . "/" . $file_name)) {
                $this->compressed_file_name = $file_name;
                break;
            }
        }
        $this->compressed_file_path = $this->base_folder . "/raw/" . $this->compressed_file_name . "." . $this->compressed_file_type;
        if (!move_uploaded_file($file["tmp_name"], $this->compressed_file_path)) {
            throw new exception("UNKNOWN_ERROR");
        }
    }
    
    
    private function extract_file_content() {
        $this->current_folder = $this->base_folder . "/" . $this->compressed_file_name;
        mkdir($this->current_folder, 0777, true);
        
        $zipArchive = new ZipArchive;
        $current_file = $zipArchive->open($this->compressed_file_path);
        
        if(!$current_file) {
            throw new exception("EXTRACTION_FAILED");
        }
        
        $zipArchive->extractTo($this->current_folder);
        $zipArchive->close();
        unlink($this->compressed_file_path);
    }
    
    private function file_clean_up() {
        if(file_exists($this->compressed_file_path)) {
            unlink($this->compressed_file_path);
        }
        
        if(file_exists($this->current_folder)) {
            $this->delete_directory($this->current_folder);
        }
    }
    
    public function delete_directory($path) {
        if (!is_dir($path)) {
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
}