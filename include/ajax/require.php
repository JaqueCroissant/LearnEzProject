<?php
session_start();
require_once '../../include/extra/global.function.php';
require_once '../../include/extra/db.class.php';
require_once '../../include/handler/handler.php';
require_once '../../include/handler/dbHandler.php';
require_once '../../include/handler/sessionKeyHandler.php';
require_once '../../include/handler/errorHandler.php';
require_once '../../include/handler/translationHandler.php';
require_once '../../include/handler/paginationHandler.php';
require_once '../../include/handler/notificationHandler.php';
require_once '../../include/handler/rightsHandler.php';
require_once '../../include/handler/settingsHandler.php';
require_once '../../include/handler/achievementHandler.php';
require_once '../../include/extra/resize.php';
require_once '../../include/extra/MP4Info.php';

require_once '../../include/class/orm.class.php';
require_once '../../include/class/error.class.php';
require_once '../../include/class/notification.class.php';
require_once '../../include/class/page.class.php';
require_once '../../include/class/rights.class.php';
require_once '../../include/class/school.class.php';
require_once '../../include/class/school_class.class.php';
require_once '../../include/class/user.class.php';
require_once '../../include/class/user_settings.class.php';
require_once '../../include/class/mail.class.php';
require_once '../../include/class/mail_folder.class.php';
require_once '../../include/class/mail_tag.class.php';
require_once '../../include/class/course.class.php';
require_once '../../include/class/lecture.class.php';
require_once '../../include/class/test.class.php';
require_once '../../include/class/calendar_date.class.php';
require_once '../../include/class/homework.class.php';
require_once '../../include/class/achievement.class.php';
require_once '../../include/class/certificate.class.php';

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    die("Direct access not permitted");
}