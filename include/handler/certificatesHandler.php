<?php

class CertificatesHandler extends Handler {

    public $current_certificate;
    public $certificates;
    public $current_code;

    public static function is_completed($course_id) {
        if (empty($course_id) || !is_numeric($course_id)) {
            throw new Exception("INVALID_INPUT");
        }

        return DbHandler::get_instance()->count_query("SELECT validation_code FROM certificates WHERE user_id = :user AND course_id = :course", SessionKeyHandler::get_from_session("user", true)->id, $course_id) == 1;
    }

    public static function create($course_id) {
        if (empty($course_id) || !is_numeric($course_id)) {
            throw new Exception("INVALID_INPUT");
        }
        DbHandler::get_instance()->query("INSERT INTO certificates VALUES (null, :user, :course, :code, NOW())", SessionKeyHandler::get_from_session("user", true)->id, $course_id, self::generate_code());
    }

    private static function generate_code() {
        $charid = md5(uniqid(rand(), true));
        $hyphen = chr(45); // "-"
        return substr($charid, 0, 4) . $hyphen . substr($charid, 4, 4) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4);
    }

    public function get_from_code($code) {
        try {
            if (!$this->check_validation_code($code)) {
                throw new Exception("INVALID_INPUT");
            }
            $temp = DbHandler::get_instance()->return_query("SELECT course.id AS course_id, course.color as course_color, course_image.filename as course_image, certificates.completion_date, certificates.validation_code, certificates.id, translation_course.title AS course_title, translation_course.description AS course_description, users.firstname AS user_firstname, users.surname AS user_surname FROM course INNER JOIN certificates ON certificates.course_id = course.id AND certificates.validation_code = :code INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN course_image ON course_image.id = course.image_id INNER JOIN users ON users.id = certificates.user_id", $code, TranslationHandler::get_current_language());
            if (empty($temp)) {
                throw new Exception("CERTIFICATE_DOESNT_EXIST");
            }
            $this->current_certificate = new Certificate(reset($temp));
            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_from_id($id) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            
            if(!is_numeric($id) && !is_int((int)$id)) {
                throw new Exception("INVALID_INPUT");
            }
            
            $data = DbHandler::get_instance()->return_query("SELECT course.id AS course_id, course.color as course_color, course_image.filename as course_image, certificates.completion_date, certificates.validation_code, certificates.id, translation_course.title AS course_title, translation_course.description AS course_description FROM course INNER JOIN certificates ON certificates.course_id = course.id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN course_image ON course_image.id = course.image_id WHERE certificates.id = :id AND certificates.user_id = :user_id", TranslationHandler::get_current_language(), $id, $this->_user->id);
            if(empty($data)) {
                throw new Exception("CERTIFICATE_DOESNT_EXIST");
            }
            
            $this->current_certificate = new Certificate(reset($data));
            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function get_multiple_by_id($ids = array()) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }

            if(empty($ids) || !is_array($ids)) {
                throw new Exception("INVALID_INPUT");
            }
            
            foreach($ids as $key => $value) {
                if(!is_numeric($value) && !is_int((int)$value)) {
                    unset($ids[$key]);
                }
            }
            
            if(empty($ids)) {
                throw new Exception("INVALID_INPUT");
            }
            
            
            $data = DbHandler::get_instance()->return_query("SELECT course.id AS course_id, course.color as course_color, course_image.filename as course_image, certificates.completion_date, certificates.validation_code, certificates.id, translation_course.title AS course_title, translation_course.description AS course_description FROM course INNER JOIN certificates ON certificates.course_id = course.id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN course_image ON course_image.id = course.image_id WHERE certificates.id IN (". generate_in_query($ids) .") AND certificates.user_id = :user_id", TranslationHandler::get_current_language(), $this->_user->id);
            
            if(empty($data)) {
                throw new Exception("CERTIFICATE_DOESNT_EXIST");
            }
            
            $this->certificates = array();
            foreach($data as $value) {
                $this->certificates[] = new Certificate($value);
            }
            
            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    public function construct_code($array){
        try  {
            if (empty($array)) {
                throw new Exception("INVALID_INPUT");
            }
            $final = "";
            foreach ($array as $value) {
                if (strlen($value) == 4) {
                    $final .= $value . "-";
                }
                else {
                    throw new Exception("INVALID_INPUT");
                }
            }
            $this->current_code = rtrim($final, "-");
            return true;
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }

    private function check_validation_code($code) {
        $temp = explode("-", $code);
        if (count($temp) != 5) {
            return false;
        }
        foreach ($temp as $value) {
            if (!(strlen($value) == 4 && ctype_alnum($value))) {
                return false;
            }
        }
        return true;
    }

    public function get_all_certificates($offset = 0, $order_ascending = 0, $complete_incomplete_all = 0) {
        try {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
            
            if (!is_numeric($offset)) {
                throw new Exception("INVALID_INPUT");
            }
            
            if (!is_numeric($order_ascending) || !is_numeric($complete_incomplete_all)) {
                throw new exception();
            }
            
            $order_by = $order_ascending == 1 ? " ORDER BY certificates.completion_date ASC" : " ORDER BY certificates.completion_date DESC";
            
            if($this->_user->user_type_id == 1) {
                $query = "SELECT course.id AS course_id, course.color as course_color, course_image.filename as course_image, certificates.completion_date, certificates.validation_code, certificates.id, translation_course.title AS course_title, translation_course.description AS course_description FROM course LEFT JOIN certificates ON certificates.course_id = course.id AND certificates.user_id = :user INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN course_image ON course_image.id = course.image_id " . $order_by;
                $temp = DbHandler::get_instance()->return_query($query, $this->_user->id,  TranslationHandler::get_current_language());
            } else {
                $query = "SELECT course.id AS course_id, course.color as course_color, course_image.filename as course_image, certificates.completion_date, certificates.validation_code, certificates.id, translation_course.title AS course_title, translation_course.description AS course_description FROM course LEFT JOIN certificates ON certificates.course_id = course.id AND certificates.user_id = :user INNER JOIN school_course ON school_course.school_id = :school AND school_course.course_id = course.id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN course_image ON course_image.id = course.image_id " . $order_by;
                $temp = DbHandler::get_instance()->return_query($query, $this->_user->id, $this->_user->school_id, TranslationHandler::get_current_language());
            }
            
            if(empty($temp)) {
                throw new Exception("NO_CERTIFICATES");
            }
            
            $array = array();
            foreach ($temp as $value) {
                $certificate = new Certificate($value);
                if(!empty($certificate->id)) {
                    $certificate->is_completed = true;
                }
                
                switch($complete_incomplete_all) {
                    case "1":
                        if(empty($certificate->id)) {
                            array_push($array, $certificate);
                        }
                        break;
                    case "2":
                        if(!empty($certificate->id)) {
                            array_push($array, $certificate);
                        }
                        break;
                    default:
                        array_push($array, $certificate);
                        break;
                }
            }
            $this->certificates = $array;
            return true;
            
        } catch (Exception $ex) {
            $this->error = ErrorHandler::return_error($ex->getMessage());
            return false;
        }
    }
    
    

}
