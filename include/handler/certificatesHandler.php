<?php

class certificatesHandler extends Handler {

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
            $temp = DbHandler::get_instance()->return_query("SELECT certificates.course_id, certificates.completion_date, certificates.validation_code, users.firstname AS user_firstname, users.surname AS user_surname, translation_course.title AS course_title, translation_course.description AS course_description FROM certificates INNER JOIN users ON users.id = certificates.user_id INNER JOIN translation_course ON translation_course.course_id = certificates.course_id WHERE certificates.validation_code = :code", $code);
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
    
    public function construct_code($array){
        try  {
            if (!$this->user_exists()) {
                throw new Exception("USER_NOT_LOGGED_IN");
            }
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
                $temp = DbHandler::get_instance()->return_query($query, $this->_user->id,  $this->_user->settings->language_id);
            } else {
                $query = "SELECT course.id AS course_id, course.color as course_color, course_image.filename as course_image, certificates.completion_date, certificates.validation_code, certificates.id, translation_course.title AS course_title, translation_course.description AS course_description FROM course LEFT JOIN certificates ON certificates.course_id = course.id AND certificates.user_id = :user INNER JOIN school_course ON school_course.school_id = :school AND school_course.course_id = course.id INNER JOIN translation_course ON translation_course.course_id = course.id AND translation_course.language_id = :language INNER JOIN course_image ON course_image.id = course.image_id " . $order_by;
                $temp = DbHandler::get_instance()->return_query($query, $this->_user->id, $this->_user->school_id, $this->_user->settings->language_id);
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
            echo $this->error->title;
            return false;
        }
    }
    
    

}
