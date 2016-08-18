<?php

class AchievementHandler extends Handler {

    public $user_id;
    public $user_achievements = [];

    public function __construct() {
        parent::__construct();
    }

    //<editor-fold defaultstate="collapsed" desc="CREATE">
    public function create_achievement($type_id, $translation_array, $breakpoint = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if ($this->_user->user_type_id != "1") {
                throw new exception("INSUFFICIENT_RIGHTS");
            }
            if (!is_numeric($breakpoint) && !is_array($translation_array)) {
                throw new exception("INVALID_INPUT");
            }
            $this->verify_type_exist($type_id);
            $query = "INSERT INTO achievement (achievement_type_id, breakpoint) VALUES (:achievement_type_id, :breakpoint)";
            $translation_query = "INSERT INTO translation_achievement (achievement_id, language_id, text) VALUES (:achievement_id, :language, :text)";
            if (!DbHandler::get_instance()->query($query, $type_id, $breakpoint)) {
                throw new Exception("DEFAULT");
            }
            $last_id = DbHandler::get_instance()->last_inserted_id();
            foreach ($translation_array as $value) {
                if (!is_numeric($value['language_id']) && !empty($value['title']) && !empty($value['text'])) {
                    throw new exception("INVALID_INPUT");
                }
                if (!DbHandler::get_instance()->query($translation_query, $last_id, $value['language_id'], $value['text'])) {
                    throw new Exception("DEFAULT");
                }
            }
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="READ">
    public function get($user_id = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            $this->user_id = false;
            if ($user_id != 0) {
                $this->set_user_id($user_id);
            }
            $this->get_completion_stats();
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_completion_stats() {
        if (!$this->user_id) {
            $query = "Select count(*) as amount, breakpoint, achievement.id as achievement_id, user_achievement.users_id as users_id, translation_achievement_type.title as title, translation_achievement.text as text from user_achievement inner join achievement on user_achievement.achievement_id = achievement.id inner join translation_achievement on achievement.id = translation_achievement.achievement_id AND translation_achievement.language_id = :language INNER JOIN achievement_type ON achievement.achievement_type_id = achievement_type.id INNER JOIN translation_achievement_type ON achievement_type.id = translation_achievement_type.achievement_type_id AND translation_achievement_type.language_id = :language INNER JOIN group by achievement_id, title, text ";
            $data = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), TranslationHandler::get_current_language());
        } else {
            $query = "Select count(*) as amount, breakpoint, achievement.id as achievement_id, user_achievement.users_id as users_id, translation_achievement_type.title as title, translation_achievement.text as text from user_achievement inner join achievement on user_achievement.achievement_id = achievement.id inner join translation_achievement on achievement.id = translation_achievement.achievement_id AND translation_achievement.language_id = :language INNER JOIN achievement_type ON achievement.achievement_type_id = achievement_type.id INNER JOIN translation_achievement_type ON achievement_type.id = translation_achievement_type.achievement_type_id AND translation_achievement_type.language_id = :language WHERE users_id = :users_id group by achievement_id, title, text";
            $data = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), TranslationHandler::get_current_language(), $this->user_id);
        }
        $this->user_achievements = [];
        foreach ($data as $value) {
            for ($index = 0; $index < $value['amount']; $index++) {
                $this->user_achievements[] = new Achievement($value);
            }
        }
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="UPDATE">
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="DELETE">
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="PRIVATE SETTERS">
    private function set_user_id($user_id) {
        if (!is_numeric($user_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
        $this->verify_user_exist($user_id);
        $this->user_id = $user_id;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="PRIVATE VERIFIERS">
    private function verify_user_exist($user_id) {
        $count = DbHandler::get_instance()->count_query("SELECT * from users where id = :id", $user_id);
        if ($count == 0) {
            throw new Exception("USER_INVALID_ID");
        }
    }
    
    private function verify_type_exist($type_id) {
        if (!is_numeric($type_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
        $count = DbHandler::get_instance()->count_query("SELECT * FROM achievement_type where id = :id", $type_id);
        if ($count == 0) {
            throw new Exception("NO_RECORD_FOUND");
        }
    }
    //</editor-fold>
}
