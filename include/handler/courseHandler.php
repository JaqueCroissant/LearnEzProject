<?php
    class courseHandler extends Handler
    {
        public static function get_os_options(){
            return DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title "
                    . "FROM course_os "
                    . "INNER JOIN translation_course_os "
                    . "ON translation_course_os.course_os_id = course_os.id "
                    . "AND translation_course_os.language_id = :language", TranslationHandler::get_current_language());    
        }
    }
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

