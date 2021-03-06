<?php

class AchievementHandler extends Handler {

    public $user_id;
    public $user_achievements = [];
    public $not_achieved = [];
    public $min_max;
    public static $temp_achievement;
    private static $course_id = null;
    private static $breakpoints = [];

    const LECTURE = "lecture";
    const TESTS = "test";
    const POINTS = "points";
    const LOGIN = "login";
    const COURSE = "course";

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

    public static function assign_achievement($type, $course_id = 0) {
        try {
            if (!SessionKeyHandler::session_exists("user")) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            if (!is_string($type)) {
                throw new exception("INVALID_INPUT");
            }
            switch (strtolower($type)) {
                case "lecture":
                    $table = "user_course_" . strtolower(self::LECTURE);
                    $achievement_type_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM achievement_type WHERE title LIKE '%" . strtolower(self::LECTURE) . "%'"))['id'];
                    self::handle_lecture_test($table, $achievement_type_id);
                    break;
                case "test":
                    $table = "user_course_" . strtolower(self::TESTS);
                    $achievement_type_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM achievement_type WHERE title LIKE '%" . strtolower(self::TESTS) . "%'"))['id'];
                    self::handle_lecture_test($table, $achievement_type_id);
                    break;
                case "points":
                    $table = "users";
                    $achievement_type_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM achievement_type WHERE title LIKE '%" . strtolower(self::POINTS) . "%'"))['id'];
                    self::handle_points($table, $achievement_type_id);
                    break;
                case "course":
                    self::set_course_id($course_id);
                    $achievement_type_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM achievement_type WHERE title LIKE '%" . strtolower(self::COURSE) . "%'"))['id'];
                    self::handle_course($achievement_type_id);
                    break;
                case "login":
                    $achievement_type_id = reset(DbHandler::get_instance()->return_query("SELECT id FROM achievement_type WHERE title LIKE '%" . strtolower(self::LOGIN) . "%'"))['id'];
                    self::handle_login($achievement_type_id);
                    break;
                default:
                    return false;
            }
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    private static function handle_login($achievement_type_id) {
        $award_types = DbHandler::get_instance()->return_query("SELECT id, award_type_id, max_days, group_concat(id) as ids, group_concat(breakpoint) as breakpoint FROM achievement WHERE achievement_type_id = :type group by award_type_id order by award_type_id", $achievement_type_id);
        foreach ($award_types as $value) {
            switch ($value['award_type_id']) {
                case "1":
                    $breakpoints = [];
                    $breakpoints = self::get_breakpoints($value);
                    $achieved_data = self::get_achieved_data($achievement_type_id, $value);
                    $q = "SELECT text from translation_achievement where achievement_id = :id and language_id = :lang_id";
                    $id = 6;
                    $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, $id, TranslationHandler::get_current_language())));
                    if ($achieved_data['text'] == "") {
                        $q = "SELECT text from translation_achievement where achievement_id = :id and language_id = :id";
                        $id = isset($achieved_data['ids']) && is_array($achieved_data['ids']) ? $achieved_data['ids'][0] : isset($achieved_data['id']) ? $achieved_data['id'] : 7;
                        $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, $id, TranslationHandler::get_current_language())));
                    }
                    $login_records = DbHandler::get_instance()->return_query("SELECT date(time) as date FROM login_record WHERE users_id = :users_id group by date order by date DESC", SessionKeyHandler::get_from_session("user", TRUE)->id);
                    if (count($login_records) < (int) $achieved_data['breakpoint']) {
                        return;
                    }
                    $bp_array = explode(",", $achieved_data['breakpoints']);
                    rsort($bp_array);
                    foreach ($breakpoints as $value_n) {
                        $total = self::check_consecutive_days($login_records, $value_n['breakpoint']);
                        if ($value_n['breakpoint'] <= $bp_array[0]) {
                            continue;
                        } else if ($value_n['breakpoint'] > $total) {
                            break;
                        } else if ($value_n['breakpoint'] <= $total) {
                            $achieved_data['current_breakpoint'] = $value_n['breakpoint'];
                            self::add_achievement_for_user($value_n['id'], $achieved_data, $value['award_type_id']);
                        }
                    }
                    break;
                case "2":
                    $query = "select *, count(*) as amount, sum(breakpoint) as sum FROM achievement_view where language_id = :language_id and users_id = :user_id AND achievement_type_id = :type AND award_type_id = :award_type_id";
                    $data = reset(DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_type_id, $value['award_type_id']));
                    if ($data['amount'] == "0") {
                        $data = self::get_dirty_object($achievement_type_id, $value['id']);
                        $data['amount'] = "0";
                        $data['sum'] = "0";
                    }
                    $login_records = DbHandler::get_instance()->return_query("SELECT date(time) as date FROM login_record WHERE users_id = :users_id group by date order by date DESC", SessionKeyHandler::get_from_session("user", TRUE)->id);
                    if (count($login_records) < (int) $data['breakpoint'] || count($login_records) < $data['sum']) {
                        break;
                    }
                    $cons_days = self::check_consecutive_days($login_records, $value['max_days']);
                    if (in_array($cons_days, self::$breakpoints[SessionKeyHandler::get_from_session("user", TRUE)->id])) {
                        break;
                    }
                    if ($cons_days - $data['sum'] >= $data['current_breakpoint']) {
                        self::add_achievement_for_user($value['id'], $data, $value['award_type_id']);
                    }
                    break;
                case "3":
                    self::handle_first_time($value['id'], self::get_dirty_object($achievement_type_id, $value['id']));
                    break;
            }
        }
    }

    private static function handle_course($achievement_type_id) {
        $award_types = DbHandler::get_instance()->return_query("SELECT id, award_type_id, max_days, group_concat(id) as ids, group_concat(breakpoint) as breakpoint FROM achievement WHERE achievement_type_id = :type group by award_type_id, max_days", $achievement_type_id);
        foreach ($award_types as $value) {
            switch ($value['award_type_id']) {
                case "1":
                    $breakpoints = self::get_breakpoints($value);
                    $achieved_data = self::get_achieved_data($achievement_type_id, $value);
                    $q = "SELECT text from translation_achievement where achievement_id = :id and language_id = :lang_id";
                    $id = 4;
                    $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, $id, TranslationHandler::get_current_language())));
                    $total = DbHandler::get_instance()->count_query("SELECT * FROM certificates WHERE user_id = :user_id", SessionKeyHandler::get_from_session("user", TRUE)->id);
                    foreach ($breakpoints as $value_n) {
                        if ($value_n['breakpoint'] < $achieved_data['sum']) {
                            continue;
                        } else if ($value_n['breakpoint'] > $total) {
                            break;
                        } else if ($value_n['breakpoint'] <= $total) {
                            self::add_achievement_for_user($value_n['id'], $achieved_data, $value['award_type_id']);
                        }
                    }
                    break;
                case "2":
                    $query = "select *, count(*) as amount, sum(breakpoint) as sum FROM achievement_view where language_id = :language_id and users_id = :user_id AND achievement_type_id = :type AND award_type_id = :award_type_id";
                    $data = reset(DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_type_id, $value['award_type_id']));
                    if ($data['amount'] == "0") {
                        $data = self::get_dirty_object($achievement_type_id, $value['id']);
                        $data['amount'] = "0";
                        $data['sum'] = "0";
                    }
                    $total = DbHandler::get_instance()->count_query("SELECT * FROM certificates WHERE user_id = :user_id", SessionKeyHandler::get_from_session("user", TRUE)->id);
                    if (in_array($total, self::$breakpoints[SessionKeyHandler::get_from_session("user", TRUE)->id])) {
                        break;
                    }
                    $total_given = $data['sum'] + $data['current_breakpoint'];
                    if ($total >= $total_given) {
                        self::add_achievement_for_user($value['id'], $data, $value['award_type_id']);
                    }
                    break;
                case "3":
                    self::handle_first_time($value['id'], self::get_dirty_object($achievement_type_id, $value['id']));
                    break;
            }
        }
    }

    private static function handle_points($table, $achievement_type_id) {
        $award_types = DbHandler::get_instance()->return_query("SELECT id, award_type_id, max_days, group_concat(id) as ids, group_concat(breakpoint) as breakpoint FROM achievement WHERE achievement_type_id = :type group by award_type_id, max_days", $achievement_type_id);
        foreach ($award_types as $value) {
            switch ($value['award_type_id']) {
                case "1":
                    $breakpoints = self::get_breakpoints($value);
                    $achieved_data = self::get_achieved_data($achievement_type_id, $value);
                    $q = "SELECT text from translation_achievement where achievement_id = :id and language_id = :lang_id";
                    $id = 12;
                    $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, $id, TranslationHandler::get_current_language())));
                    $total = reset(reset(DbHandler::get_instance()->return_query("SELECT points FROM users WHERE id = :user_id", SessionKeyHandler::get_from_session("user", TRUE)->id)));
                    $bp_array = explode(",", $achieved_data['breakpoints']);
                    rsort($bp_array);
                    foreach ($breakpoints as $value_n) {
                        $total = self::check_consecutive_days($login_records, $value_n['breakpoint']);
                        if ($value_n['breakpoint'] <= $bp_array[0]) {
                            continue;
                        } else if ($value_n['breakpoint'] > $total) {
                            break;
                        } else if ($value_n['breakpoint'] <= $total) {
                            $achieved_data['current_breakpoint'] = $value_n['breakpoint'];
                            self::add_achievement_for_user($value_n['id'], $achieved_data, $value['award_type_id']);
                        }
                    }
                    break;
                case "2":
                    $query = "select *, count(*) as amount, sum(breakpoint) as sum FROM achievement_view where language_id = :language_id and users_id = :user_id AND achievement_type_id = :type AND award_type_id = :award_type_id";
                    $data = reset(DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_type_id, $value['award_type_id']));
                    if ($data['amount'] == "0") {
                        $data = self::get_dirty_object($achievement_type_id, $value['id']);
                        $data['amount'] = "0";
                        $data['sum'] = "0";
                    }
                    $current_points = reset(DbHandler::get_instance()->return_query("SELECT points FROM " . $table . " WHERE id = :user_id", SessionKeyHandler::get_from_session("user", TRUE)->id))['points'];
                    $total_given = $data['sum'] + $data['current_breakpoint'];
                    if ($current_points >= $total_given) {
                        self::add_achievement_for_user($value['id'], $data, $value['award_type_id']);
                    }
                    break;
                case "3":
                    self::handle_first_time($value['id'], self::get_dirty_object($achievement_type_id, $value['id']));
                    break;
            }
        }
    }

    private static function handle_lecture_test($table, $achievement_type_id) {
        $award_types = DbHandler::get_instance()->return_query("SELECT id, award_type_id, max_days, group_concat(id) as ids, group_concat(breakpoint) as breakpoint FROM achievement WHERE achievement_type_id = :type group by award_type_id, max_days", $achievement_type_id);
        $total = DbHandler::get_instance()->count_query("SELECT * FROM " . $table . " WHERE user_id = :user_id AND is_complete = 1", SessionKeyHandler::get_from_session("user", TRUE)->id);
        foreach ($award_types as $value) {
            switch ($value['award_type_id']) {
                case "1":
                    $breakpoints = self::get_breakpoints($value);
                    $achieved_data = self::get_achieved_data($achievement_type_id, $value);
                    if ($achievement_type_id == "1") {
                        $q = "SELECT text from translation_achievement where achievement_id = :id and language_id = :lang_id";
                        $id = 15;
                        $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, $id, TranslationHandler::get_current_language())));
                    } else {
                        $q = "SELECT text from translation_achievement where achievement_id = :id and language_id = :lang_id";
                        $id = 3;
                        $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, $id, TranslationHandler::get_current_language())));
                    }
                    $bp_array = explode(",", $achieved_data['breakpoints']);
                    rsort($bp_array);
                    foreach ($breakpoints as $value_n) {
                        $total = self::check_consecutive_days($login_records, $value_n['breakpoint']);
                        if ($value_n['breakpoint'] <= $bp_array[0]) {
                            continue;
                        } else if ($value_n['breakpoint'] > $total) {
                            break;
                        } else if ($value_n['breakpoint'] <= $total) {
                            $achieved_data['current_breakpoint'] = $value_n['breakpoint'];
                            self::add_achievement_for_user($value_n['id'], $achieved_data, $value['award_type_id']);
                        }
                    }
                    break;
                case "2":
                    $achieved_data = self::get_achieved_data($achievement_type_id, $value);
                    $q = "SELECT text from translation_achievement inner join achievement on translation_achievement.achievement_id = achievement.id where language_id = :lang_id and achievement_type_id = :ach_id limit 1";
                    $achieved_data['text'] = reset(reset(DbHandler::get_instance()->return_query($q, TranslationHandler::get_current_language(), $achievement_type_id)));
                    $count = $achieved_data['sum'] + $achieved_data['current_breakpoint'];
                    if ($total >= $count) {
                        $achieved_data['breakpoint'] = $count;
                        self::add_achievement_for_user($value['id'], $achieved_data, $value['award_type_id']);
                    }
                    break;
                case "3":
                    self::handle_first_time($value['id'], self::get_dirty_object($achievement_type_id, $value['id']));
                    break;
            }
        }
    }

    private static function handle_first_time($achievement_id, $data_object) {
        if (self::$course_id) {
            $count = DbHandler::get_instance()->count_query("SELECT * from user_achievement WHERE users_id = :user_id AND achievement_id = :ach_id", SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_id);
        } else {
            $count = DbHandler::get_instance()->count_query("SELECT * from user_achievement WHERE users_id = :user_id AND achievement_id = :ach_id AND value_id = :course_id", SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_id, self::$course_id);
        }

        if ($count == 0) {
            self::add_achievement_for_user($achievement_id, $data_object, "3");
        }
    }

    private static function add_achievement_for_user($achievement_id, $data_object, $award_type_id) {
        $cur_breakpoint = isset($data_object['current_breakpoint']) ? $data_object['current_breakpoint'] : 0;
        echo $achievement_id . '<br/>';
        if (self::$course_id) {
            $query = "INSERT INTO user_achievement (users_id, achievement_id, breakpoint, value_id) VALUES (:user, :ach_id, :breakpoint, :value_id)";
            DbHandler::get_instance()->query($query, SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_id, $cur_breakpoint, self::$course_id);
        } else {
            $query = "INSERT INTO user_achievement (users_id, achievement_id, breakpoint) VALUES (:user, :ach_id, :breakpoint)";
            DbHandler::get_instance()->query($query, SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_id, $cur_breakpoint);
        }
        $data_object['award_type_id'] = $award_type_id;
        self::set_cookie($data_object);
    }

    private static function check_consecutive_days($days_array, $max_days) {
        $last_x_days = [];
        $format = "Y-m-d";
        for ($index = 0; $index < $max_days + 1; $index++) {
            $last_x_days[] = date($format, strtotime("-" . $index . " days"));
        }
        $j = 0;
        $max = count($days_array) >= $max_days ? $max_days : count($days_array);
        for ($i = 0; $i < $max; $i++) {
            if ($days_array[$i]['date'] != $last_x_days[$i]) {
                return $j;
            }
            $j++;
        }
        return $j;
    }

    private static function get_dirty_object($achievement_type_id, $achievement_id) {
        $query = "SELECT breakpoint as current_breakpoint, breakpoint as breakpoint, text, path as img_path, o_top, o_left from achievement inner join translation_achievement on achievement.id = translation_achievement.achievement_id INNER JOIN achievement_img on achievement.achievement_img_id = achievement_img.id WHERE achievement.achievement_type_id = :achievement_type_id and language_id = :lang_id and achievement.id = :achievement_id";
        $data_object = reset(DbHandler::get_instance()->return_query($query, $achievement_type_id, TranslationHandler::get_current_language(), $achievement_id));
        $data_object['id'] = $achievement_id;
        $data_object['achievement_type_id'] = $achievement_type_id;
        return $data_object;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="READ">
    public function get($user_id = 0, $ach_ids = 0) {
        try {
            if (!$this->user_exists()) {
                throw new exception("USER_NOT_LOGGED_IN");
            }
            $this->user_id = false;
            if ($user_id != 0) {
                $this->set_user_id($user_id);
            }
            if ($ach_ids != 0 && is_array($ach_ids)) {
                unset($this->user_achievements);
                $this->user_achievements = array();
                foreach ($ach_ids as $value) {
                    if (!is_numeric($value)) {
                        throw new Exception("INVALID_INPUT_IS_NOT_INT");
                    }
                }
                $ids = generate_in_query($ach_ids);
                $q = "SELECT * from achievement_view WHERE user_achievement_id IN (" . $ids . ") and (language_id = :lang OR language_id is null)";
                $var = DbHandler::get_instance()->return_query($q, TranslationHandler::get_current_language());
                foreach ($var as $nv) {
                    $nv['id'] = $nv['user_achievement_id'];
                    if ($nv['achievement_type_id'] == "4" && $value['award_type_id'] == "3") {
                        $nv = $this->get_course_title($nv);
                    } else if ($value['achievement_type_id'] == "4") {
                        $value['text'] = $value['breakpoint'] . " " . strtolower($value['text']);
                    }
                    $this->user_achievements[] = new Achievement($nv);
                }
                return true;
            } else if ($ach_ids != 0) {
                unset($this->user_achievements);
                $q = "SELECT * from achievement_view WHERE user_achievement_id = :id and (language_id = :lang OR language_id is null)";
                $var = reset(DbHandler::get_instance()->return_query($q, $ach_ids, TranslationHandler::get_current_language()));
                $var['id'] = $var['user_achievement_id'];
                if ($var['achievement_type_id'] == "4" && $value['award_type_id'] == "3") {
                    $var = $this->get_course_title($var);
                } else if ($value['achievement_type_id'] == "4") {
                    $value['text'] = $value['breakpoint'] . " " . strtolower($value['text']);
                }
                $this->user_achievements = new Achievement($var);
                return true;
            }
            $this->get_completion_stats();
            return true;
        } catch (Exception $exc) {
            $this->error = ErrorHandler::return_error($exc->getMessage());
            return false;
        }
    }

    private function get_completion_stats() {
        $query = "select * FROM achievement_view where (language_id = :language_id OR language_id is NULL) AND users_id = :users_id order by user_achievement_id";
        if (!$this->user_id) {
            $data = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), $this->_user->id);
        } else {
            $data = DbHandler::get_instance()->return_query($query, TranslationHandler::get_current_language(), $this->user_id);
        }
        $this->user_achievements = "";
        $ach_ids = [];
        $temp = [];
        foreach ($data as $value) {
            if ($value['award_type_id'] != "2") {
                $ach_ids[] = $value['achievement_id'];
            }
            $value['id'] = $value['user_achievement_id'];
            if (!isset($temp[$value['achievement_type_id']])) {
                $temp[$value['achievement_type_id']] = array();
            }
            if (!isset($temp[$value['achievement_type_id']]['breakpoint'])) {
                $temp[$value['achievement_type_id']]['breakpoint'] = 0;
            }
            if ($value['award_type_id'] == "2") {
                $temp[$value['achievement_type_id']]['breakpoint'] += $value['breakpoint'];
                $value['breakpoint'] = $temp[$value['achievement_type_id']]['breakpoint'];
            }
            if ($value['text'] == "" && $value['achievement_type_id'] == "4" && $value['award_type_id'] == "3") {
                $value = $this->get_course_title($value);
            } else {
                $value['text'] = $value['breakpoint'] . " " . strtolower($value['text']);
            }
            $this->user_achievements[$value['achievement_type_id']][] = new Achievement($value);
        }
        $this->get_min_max();
        $this->get_not_completed($ach_ids, $temp);
    }

    public function get_min_max() {
        $min_q = "SELECT count(*) as count, achievement_type_id as type_id FROM user_achievement inner join achievement on user_achievement.achievement_id = achievement.id where users_id = :user_id group by type_id";
        if ($this->user_id) {
            $min_data = DbHandler::get_instance()->return_query($min_q, $this->user_id);
        } else {
            $min_data = DbHandler::get_instance()->return_query($min_q, $this->_user->id);
        }
        $max_q = "SELECT count(*) as count, achievement_type_id as type_id FROM achievement where award_type_id != 2 group by type_id";
        $max_data = DbHandler::get_instance()->return_query($max_q);
        $this->min_max = array();

        for ($h = 1; $h < 6; $h++) {
            $this->min_max[$h] = array();
            $this->min_max[$h]['current'] = 0;
            $this->min_max[$h]['max'] = 0;
        }
        foreach ($min_data as $min) {
            $this->min_max[$min['type_id']]['current'] = $min['count'];
        }
        foreach ($max_data as $max) {
            $this->min_max[$max['type_id']]['max'] = $max['count'];
        }
    }

    private function get_not_completed($id_arr, $breakpoints) {
        if (count($id_arr) == 0) {
            $not_q = "SELECT achievement.*, translation_achievement.text as text, achievement_type.title as achievement_type_title, path as img_path FROM achievement left join translation_achievement on achievement.id = translation_achievement.achievement_id inner join achievement_type on achievement.achievement_type_id = achievement_type.id inner join achievement_img on achievement_img_id = achievement_img.id where (language_id = :id OR language_id is null) order by breakpoint ";
        } else {
            $ids = generate_in_query($id_arr);
            $not_q = "SELECT achievement.*, translation_achievement.text as text, achievement_type.title as achievement_type_title, path as img_path FROM achievement left join translation_achievement on achievement.id = translation_achievement.achievement_id inner join achievement_type on achievement.achievement_type_id = achievement_type.id inner join achievement_img on achievement_img_id = achievement_img.id where achievement.id NOT IN (" . $ids . ") and (language_id = :id OR language_id is null) order by breakpoint ";
        }
        $not_data = DbHandler::get_instance()->return_query($not_q, TranslationHandler::get_current_language());
//        echo '<pre>';
//        var_dump($not_data);
//        echo '</pre>';
        foreach ($not_data as $val) {
            if ($val['award_type_id'] == "2") {
                for ($i = 1; $i < 4; $i++) {
                    if ($i == 1) {
                        $text = $val['text'];
                    }
                    if ($val['award_type_id'] == "1" || $val['award_type_id'] == "2") {
                        $bp = isset($breakpoints[$val['achievement_type_id']]['breakpoint']) ? $breakpoints[$val['achievement_type_id']]['breakpoint'] : 0;
                        $val['text'] = isset($val['breakpoint']) ? ( (int) ($val['breakpoint'] * $i ) + (int) $bp ) . ' ' . strtolower($text) : $val['text'];
                    }
                    $this->not_achieved[$val['achievement_type_id']][] = new Achievement($val);
                }
            } else {
                if ($val['award_type_id'] == "1") {
                    $val['text'] = isset($val['breakpoint']) ? $val['breakpoint'] . ' ' . strtolower($val['text']) : $val['text'];
                }
                $this->not_achieved[$val['achievement_type_id']][] = new Achievement($val);
            }
        }
    }

    private function get_course_title($data) {
        if (!isset($data['value_id']) && $data['achievement_type_id'] == "4") {
            throw new Exception("DEFAULT");
        }
        $query = "SELECT title FROM translation_course where course_id = :course_id AND language_id = :lang";
        $temp = reset(reset(DbHandler::get_instance()->return_query($query, $data['value_id'], TranslationHandler::get_current_language())));
        $data['text'] = $temp . ' ' . strtolower(TranslationHandler::get_static_text("COMPLETED_ALT"));
        return $data;
    }

    private static function get_breakpoints($array) {
        $breakpoints = [];
        $ids = $array['ids'] != "" ? explode(',', $array['ids']) : 0;
        $bps = $array['breakpoint'] != "" ? explode(',', $array['breakpoint']) : 0;
        for ($i = 0; $i < count($ids); $i++) {
            $breakpoints[$i]['id'] = is_array($ids) ? $ids[$i] : 0;
            $breakpoints[$i]['breakpoint'] = is_array($bps) ? $bps[$i] : 0;
        }
        return $breakpoints;
    }

    private static function get_achieved_data($achievement_type_id, $award_type_obj) {
        $data_query = "select *, count(*) as amount, group_concat(breakpoint) as breakpoints, sum(breakpoint) as sum FROM achievement_view where (language_id = :language_id OR language_id is null) and users_id = :user_id AND achievement_type_id = :type AND award_type_id = :award_type_id";
        $achieved_data = reset(DbHandler::get_instance()->return_query($data_query, TranslationHandler::get_current_language(), SessionKeyHandler::get_from_session("user", TRUE)->id, $achievement_type_id, $award_type_obj['award_type_id']));

        if ($achieved_data['amount'] == "0") {
            $ach_id = is_array($award_type_obj['ids']) ? explode(",", $award_type_obj['ids'])[0] : $award_type_obj['ids'];
            $achieved_data = self::get_dirty_object($achievement_type_id, $ach_id);
            $achieved_data['text'] = "";
            $achieved_data['title'] = "";
            $achieved_data['amount'] = "0";
            $achieved_data['breakpoints'] = "";
            $achieved_data['sum'] = "0";
        }
        return $achieved_data;
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

    private static function set_cookie($data) {
        $cookie_name = 'achievements';
        $t = array();
        $temp = array();
        if (isset($_COOKIE[$cookie_name]) && is_array(json_decode($_COOKIE[$cookie_name]))) {
            $temp = json_decode($_COOKIE[$cookie_name]);
        } else if (isset($_COOKIE[$cookie_name])) {
            array_push($temp, json_decode($_COOKIE[$cookie_name]));
        }
        $t['img_path'] = isset($data['img_path']) ? $data['img_path'] : "default.png";
        $breakpoints = $data['breakpoints'] != "" ? explode(',', $data['breakpoints']) : "";
        if ($data['award_type_id'] == "2" && is_array($breakpoints)) {
            if ($data['current_breakpoint'] != "") {
                $t['count'] = $data['current_breakpoint'] + array_sum($breakpoints);
            } else if ($data['current_breakpoint'] != "") {
                $t['count'] = $data['current_breakpoint'];
            } else {
                $t['count'] = "";
            }
        } else {
            $t['count'] = isset($data['current_breakpoint']) ? $data['current_breakpoint'] : 0;
        }

        $t['title'] = TranslationHandler::get_static_text("NEW_ACHIEVEMENT");
        $t['text'] = isset($data['text']) ? $t['count'] . " " . strtolower($data['text']) : "";
        $t['o_top'] = isset($data['o_top']) ? $data['o_top'] : "";
        $t['o_left'] = isset($data['o_left']) ? $data['o_left'] : "";
        array_push($temp, $t);
        setcookie($cookie_name, json_encode($temp), time() + (86400 * 30), "/", false);
        $_COOKIE[$cookie_name] = json_encode($temp);
    }

    private static function set_course_id($course_id) {
        if (!is_numeric($course_id)) {
            throw new Exception("INVALID_INPUT_IS_NOT_INT");
        }
        if ($course_id == 0) {
            return;
        }
        $count = DbHandler::get_instance()->count_query("SELECT * from course where id = :id", $course_id);
        if ($count == 0) {
            throw new Exception("DEFAULT");
        }
        self::$course_id = $course_id;
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
