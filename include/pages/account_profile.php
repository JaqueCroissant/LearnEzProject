<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/mailHandler.php';
require_once '../../include/handler/statisticsHandler.php';
require_once '../../include/handler/courseHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$userHandler = new UserHandler();
$schoolHandler = new SchoolHandler();
$statisticsHandler = new StatisticsHandler();
$courseHandler = new CourseHandler();
$homeworkHandler = new HomeworkHandler();

$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : null;

if (!$userHandler->get_user_by_id($user_id)) {
    ErrorHandler::show_error_page();
    die();
}

$current_user = $userHandler->temp_user;
if (!empty($current_user->school_id)) {
    $schoolHandler->get_school_by_id($current_user->school_id);
    $current_school = $schoolHandler->school;
};
?>

<div class="profile-header" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem !important;">
    <div class="col-md-2 pull-right m-t-lg m-r-lg">

        <?php if (RightsHandler::has_user_right("ACCOUNT_AVAILABILITY")) { ?>
            <div class="pull-right p-v-xs">
                <form method="post" id="alert_form_<?php echo $current_user->id; ?>" action="" url="edit_account.php?step=set_availability">
                    <i style="width:20px; height:20px;" class="zmdi zmdi-hc-2x zmdi-hc-lg <?php echo $current_user->open == 1 ? "zmdi-close-circle" : "zmdi-check-circle" ?> btn_close_account m-r-sm a pull-right" element_state='<?php echo $current_user->open; ?>' element_id='<?php echo $current_user->id; ?>' data-toggle="tooltip" title="<?= $current_user->open == 1 ? TranslationHandler::get_static_text("CLOSE") : TranslationHandler::get_static_text("OPEN") ?>"></i>
                    <input type="hidden" name="user_id" value="<?php echo $current_user->id; ?>">
                    <input type="hidden" id='account_availability' value="<?php echo $current_user->open; ?>">
                    <input type="hidden" name="submit" value="submit">
                </form>
            </div>
        <?php } ?>
        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
            <div class="pull-right p-v-xs">
                <i style="width:20px; height:20px;" class="zmdi zmdi-hc-lg zmdi-hc-2x zmdi-edit m-r-xs change_page a" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT") ?>" page="edit_account" step="" args="&user_id=<?php echo $current_user->id; ?>"></i>
            </div>
        <?php } ?>
    </div>
    <div class="profile-cover">
        <div class="cover-user m-b-lg">
            <div>
                <a href="#achievements" style="color: #6a6c6f !important;"><span class="cover-icon" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("ACHIEVEMENTS") ?>" style="cursor:pointer"><i class="fa fa-star" style="font-size:16px !important;margin-left:2px;"></i></span></a>
            </div>
            <div>
                <div class="avatar avatar-xl avatar-circle">
                    <img class="img-responsive" src="assets/images/profile_images/<?= $current_user->image_id; ?>.png" alt="avatar">
                </div>
            </div>
            <div>
                <?php if (MailHandler::can_send_to_receiver($user_id)) { ?>
                    <span class="cover-icon change_page" id="mail" page="mail" step="create_mail" args="&receiver_id=USER_ANY_<?= $current_user->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>" style="cursor:pointer;line-height:38px !important;"><i class="fa fa-envelope" style="margin-left:1px;"></i></span>
                <?php } else { ?>
                    <span class="cover-icon"  data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>" style="cursor:not-allowed;line-height:38px !important;"><i class="fa fa-envelope" style="margin-left:1px;"></i></span>
                <?php } ?>
            </div>
        </div>
        <div class="text-center">
            <h4 class="profile-info-name m-b-lg"><span class="title-color"><?= ucwords($current_user->firstname . " " . $current_user->surname); ?></span></h4>
            <div class="text-primary">
                <span style="padding-right:10px;"><i class="zmdi-hc-fw zmdi p-r-lg zmdi-device-hub zmdi-hc-lg" style="line-height: 0.4em !important;"></i> <?= htmlentities($current_user->user_type_title); ?></span>
                <span data-toggle="tooltip" title="<?= !empty($current_user->school_id) ? htmlspecialchars($current_school->name) : "LearnEZ"; ?>" class="<?= !empty($current_user->school_id) ? "change_page a " : "" ?>" page='school_profile' step='' args='&school_id=<?= !empty($current_user->school_id) ? $current_user->school_id : "" ?>'><i class="zmdi-hc-fw zmdi p-r-lg zmdi-city zmdi-hc-lg" style="line-height: 0.4em !important;"></i><?= !empty($current_user->school_id) ? (strlen(htmlspecialchars($current_school->name)) > 40 ? substr(htmlspecialchars($current_school->name), 0, 40) : htmlspecialchars($current_school->name)) : "LearnEZ"; ?></span>
            </div>
        </div>
    </div>

    <div class="promo-footer">
        <div class="row no-gutter">
            <div class="col-sm-2 col-sm-offset-3 col-xs-6 promo-tab">
                <div class="text-center">
                    <small>Konto oprettet</small>
                    <?php $date_created = time_elapsed($current_user->time_created); ?>
                    <h4 class="m-0 m-t-xs"><?= $date_created["value"] . ' ' . TranslationHandler::get_static_text($date_created["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO"); ?></h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-6 promo-tab">
                <div class="text-center">
                    <small>Seneste login</small>
                    <?php $last_login = time_elapsed($current_user->last_login); ?>

                    <h4 class="m-0 m-t-xs"><?= $current_user->last_login == 0 ? TranslationHandler::get_static_text("NEVER") : $last_login["value"] . ' ' . TranslationHandler::get_static_text($last_login["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO"); ?></h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-12 promo-tab">
                <div class="text-center">
                    <small>Konto status</small>
                    <h4 class="m-0 m-t-xs" style="color: <?= $current_user->open ? '#36ce1c' : '#f15530'; ?>"><?= $current_user->open ? TranslationHandler::get_static_text("OPEN") : TranslationHandler::get_static_text("CLOSED"); ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md" >
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-account zmdi-hc-lg" style="padding-right:26px;"></i><?= TranslationHandler::get_static_text("USER_DESCRIPTION") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body user-description">
                <div class="center description" ><?= empty(htmlspecialchars($current_user->description)) ? TranslationHandler::get_static_text("NO_DESCRIPTION") : htmlspecialchars(nl2br($current_user->description)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md">
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-info-outline zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("INFORMATION") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body user-information">
                <table class="profile_information_table">
                    <tr>
                        <td><?= TranslationHandler::get_static_text("NAME") ?>:</td>
                        <td style="text-align:right;"><?= ucwords(htmlspecialchars($current_user->firstname) . " " . htmlspecialchars($current_user->surname)); ?></td>
                    </tr>
                    <tr>
                        <td><?= TranslationHandler::get_static_text("USERNAME") ?>:</td>
                        <td style="text-align:right;"><?= $current_user->username; ?></td>
                    </tr>
                    <tr>
                        <td><?= TranslationHandler::get_static_text("USER_TYPE") ?>:</td>
                        <td style="text-align:right;"><?= htmlspecialchars($current_user->user_type_title); ?></td>
                    </tr>
                    <tr>
                        <td><?= TranslationHandler::get_static_text("AFFILIATION") ?>:</td>
                        <td class="<?= empty($current_user->school_id) || $current_user->school_id == "0" ? "text-primary fw-600" : "" ?>" style="text-align:right;"><?= !empty($current_user->school_id) ? (strlen(htmlspecialchars($current_school->name)) > 40 ? substr(htmlspecialchars($current_school->name), 0, 40) : htmlspecialchars($current_school->name)) : "LearnEZ " . strtolower(TranslationHandler::get_static_text("STAFF")); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if ($current_user->user_type_id == "3" || $current_user->user_type_id == "4") { ?>
    <div class="row">
        <?php
        $courseHandler->get_courses($current_user->id);
        $courses_average = 0;
        $courses_completed = 0;
        $course_count = count($courseHandler->courses);
        $courses_started = $course_count;
        foreach ($courseHandler->courses as $value) {

            $courses_average += $value->overall_progress;
            if ($value->overall_progress > 99) {
                $courses_completed++;
            } else if ($value->overall_progress < 1) {
                $courses_started--;
            }
        }
        $courses_average = $course_count > 0 ? round($courses_average / $course_count, 0) : 0;
        ?>


        <div class="col-sm-12 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md" >
                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("COURSE_PROGRESS") ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body user-progress">
                    <?php
                    $i_max = 7;
                    $format = 'Y-m-d';
                    $statisticsHandler->get_student_stats($current_user->id, $i_max)
                    ?>
                    <div data-plugin="chart" data-options="{
                         tooltip : {
                         trigger: 'axis'
                         },
                         legend: {
                         data:['<?= TranslationHandler::get_static_text("LECTURES") ?>','<?= TranslationHandler::get_static_text("TESTS") ?>']
                         },
                         calculable : true,
                         xAxis : [
                         {
                         type : 'category',
                         boundaryGap : false,
                         data : [
                         <?php
                         for ($i = date('w') - 7; $i < date('w') + 1; $i++) {
                             echo "'" . TranslationHandler::get_static_text("WEEK_DAY_" . strtoupper(day_num_to_string($i))) . "'";
                             if ($i != date('w')) {
                                 echo ",";
                             }
                         }
                         ?>
                         ]
                         }
                         ],
                         yAxis : [
                         {
                         type : 'value'
                         }
                         ],
                         series : [
                         {
                         name:'<?= TranslationHandler::get_static_text("LECTURES") ?>',
                         type:'line',
                         smooth:true,
                         itemStyle: {normal: {areaStyle: {type: 'default'}}},
                         data:[<?php
                         for ($i = 0; $i < count($statisticsHandler->lecture_graph_stats); $i++) {
                             if ($i == count($statisticsHandler->lecture_graph_stats) - 1) {
                                 echo $statisticsHandler->lecture_graph_stats[$i];
                             } else {
                                 echo $statisticsHandler->lecture_graph_stats[$i] . ', ';
                             }
                         }
                         ?>]
                         },
                         {
                         name:'<?= TranslationHandler::get_static_text("TESTS") ?>',
                         type:'line',
                         smooth:true,
                         itemStyle: {normal: {areaStyle: {type: 'default'}}},
                         data:[<?php
                         for ($i = 0; $i < count($statisticsHandler->test_graph_stats); $i++) {
                             if ($i == count($statisticsHandler->test_graph_stats) - 1) {
                                 echo $statisticsHandler->test_graph_stats[$i];
                             } else {
                                 echo $statisticsHandler->test_graph_stats[$i] . ', ';
                             }
                         }
                         ?>]
                         }
                         ]
                         }" style="height: 300px;"></div>
                     <!--<div class="center progress-text" style="margin-top: 20px; margin-bottom: 20px;"><?= TranslationHandler::get_static_text("NO_COURSE_PROGRESS") ?></div>-->
                </div>
            </div>
        </div>
        <?php if ($userHandler->_user->user_type_id == "1" || $userHandler->_user->user_type_id == "2") { ?>
            <div class="col-sm-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading p-h-lg p-v-md" >
                        <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-account zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("LOGIN_ACTIVITY") ?></h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body login_activity">
                        <div data-plugin="chart" data-options="{
                             tooltip : {
                             trigger: 'axis'
                             },
                             legend: {
                             data:['<?= TranslationHandler::get_static_text("LOGIN_ACTIVITY") ?>']
                             },
                             calculable : true,
                             xAxis : [
                             {
                             type : 'category',
                             boundaryGap : false,
                             data : [
                             <?php
                             for ($i = date('w') - 7; $i < date('w') + 1; $i++) {
                                 echo "'" . TranslationHandler::get_static_text("WEEK_DAY_" . strtoupper(day_num_to_string($i))) . "'";
                                 if ($i != date('w')) {
                                     echo ",";
                                 }
                             }
                             ?>
                             ]
                             }
                             ],
                             yAxis : [
                             {
                             type : 'value'
                             }
                             ],
                             series : [
                             {
                             name:'<?= TranslationHandler::get_static_text("LOGIN_ACTIVITY") ?>',
                             type:'line',
                             smooth:true,
                             itemStyle: {normal: {areaStyle: {type: 'default'}}},
                             data:[<?php
                             for ($i = 0; $i < count($statisticsHandler->login_activity); $i++) {
                                 if ($i == count($statisticsHandler->login_activity) - 1) {
                                     echo $statisticsHandler->login_activity[$i];
                                 } else {
                                     echo $statisticsHandler->login_activity[$i] . ', ';
                                 }
                             }
                             ?>]
                             }
                             ]
                             }" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6" id="">
                <div class="panel panel-default">
                    <div class='panel-heading p-h-lg p-v-md'>
                        <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("STATISTICS"); ?> </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body statistics">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $courses_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($courses_average) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $courses_average; ?></span>%    </strong>
                                        </div>
                                    </div>
                                    <div class="media-right ">
                                        <div style="margin-left: 25px;">
                                            <label  class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("COURSE")) . " " . strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></label>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("COURSES") . " " . strtolower(TranslationHandler::get_static_text("STARTED")) . ": " . $courses_started . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $course_count; ?>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("COURSES") . " " . strtolower(TranslationHandler::get_static_text("COMPLETED")) . ": " . $courses_completed . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $course_count; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_lecture_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_lecture_average) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $statisticsHandler->student_lecture_average; ?></span>%    </strong>
                                        </div>
                                    </div>
                                    <div class="media-right ">
                                        <div style="margin-left: 25px;">
                                            <label  class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("LECTURE")) . " " . strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></label>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("LECTURES") . " " . strtolower(TranslationHandler::get_static_text("STARTED")) . ": " . $statisticsHandler->student_lectures_started . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_lectures; ?>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("LECTURES") . " " . strtolower(TranslationHandler::get_static_text("COMPLETED")) . ": " . $statisticsHandler->student_lectures_complete . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_lectures; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_test_average) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $statisticsHandler->student_test_average; ?></span>%</strong>
                                        </div>
                                    </div>
                                    <div class="media-right">
                                        <div style="margin-left: 25px;">
                                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE_TEST_PROG"); ?></label>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("TESTS_STARTED") . ": " . $statisticsHandler->student_tests_started . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_tests; ?>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("TESTS_COMPLETED") . ": " . $statisticsHandler->student_tests_complete . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_tests; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo 0; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color(0) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo 0; ?></span>%</strong>
                                        </div>
                                    </div>
                                    <div class="media-right">
                                        <div style="margin-left: 25px;">
                                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("ACHIEVEMENTS"); ?></label>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("BADGES_OBTAINED") . ": " . "NULL" . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . "NULL"; ?>
                                        </div>
                                        <div style="margin-left: 25px;">
                                            <?php echo TranslationHandler::get_static_text("TOTAL_POINTS") . ": " . $userHandler->_user->points; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="col-sm-12 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md" >
                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-star zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("ACHIEVEMENTS") ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body user-achievements">
                    <div class="center achievements-text" style="margin-top: 20px; margin-bottom: 20px;"><?= TranslationHandler::get_static_text("NO_ACHIEVEMENTS") ?></div>
                </div>
            </div>
        </div>

    </div>

<?php } ?>
<div id="alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div class="hidden" id="open_text"><?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("ACCOUNT")) . "?"; ?></div>
        <div class="hidden" id="close_text"><?php echo TranslationHandler::get_static_text("CONFIRM_OPEN") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("ACCOUNT")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_alertbox_btn" id="" page='account_profile' type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<input type="hidden" id="user_type_id_hidden" value='<?php echo $courseHandler->_user->user_type_id; ?>'>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        if ($(".user-description").height() > $(".user-information").height()) {
            $(".user-information").height($(".user-description").height());
        } else {
            var padding = Math.floor(($(".user-information").height() - $('.description').height()) / 2);
            $('.description').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
            $(".user-description").height($(".user-information").height());
        }

        if ($(".login_activity").height() > $(".user-progress").height()) {
            if ($('.progress-text').length) {
                var padding = Math.floor(($(".login_activity").height() - $('.progress-text').height()) / 2);
                $('.progress-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
            }
            $(".user-progress").height($(".login_activity").height());
        } else {
            if ($('.achievements-text').length) {
                var padding = Math.floor(($(".user-progress").height() - $('.achievements-text').height()) / 2);
                $('.achievements-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
            }
            $(".login_activity").height($(".user-progress").height());
        }

        if ($(".user-achievements").height() > $(".statistics").height()) {
            if ($('.progress-text').length) {
                var padding = Math.floor(($(".user-achievements").height() - $('.progress-text').height()) / 2);
                $('.progress-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
            }
            $(".statistics").height($(".user-achievements").height());
        } else {
            if ($('.achievements-text').length) {
                var padding = Math.floor(($(".statistics").height() - $('.achievements-text').height()) / 2);
                $('.achievements-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
            }
            $(".user-achievements").height($(".statistics").height());
        }

        if ($("#user_type_id_hidden").val() === "3" || $("#user_type_id_hidden").val() === "4") {
            if ($(".user-achievements").height() > $(".user-progress").height()) {
                if ($('.progress-text').length) {
                    var padding = Math.floor(($(".user-achievements").height() - $('.progress-text').height()) / 2);
                    $('.progress-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
                }
                $(".user-progress").height($(".user-achievements").height());
            } else {
                if ($('.achievements-text').length) {
                    var padding = Math.floor(($(".user-progress").height() - $('.achievements-text').height()) / 2);
                    $('.achievements-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
                }
                $(".user-achievements").height($(".user-progress").height());
            }

        }
    });
</script>