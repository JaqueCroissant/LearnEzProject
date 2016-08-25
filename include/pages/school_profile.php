<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$userHandler = new UserHandler();
$statisticsHandler = new StatisticsHandler();

if (!RightsHandler::has_page_right("SCHOOL_PROFILE")) {
    ErrorHandler::show_error_page(ErrorHandler::return_error("INSUFFICIENT_RIGHTS"));
}

if (isset($_GET['school_id'])) {
    if ($schoolHandler->_user->user_type_id == 1) {
        $schoolHandler->get_school_by_id($_GET['school_id']);
    } else {
        $schoolHandler->get_school_by_id($schoolHandler->_user->school_id);
    }
    $userHandler->get_by_school_id($_GET['school_id']);
    $statisticsHandler->get_average_for_school($_GET['school_id']);
} else {
    $schoolHandler->get_school_by_id($schoolHandler->_user->school_id);
}
?>
<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="profile-header" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem !important;">
    <div class="col-md-2 pull-right m-t-lg m-r-lg">

        <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
            <div class="pull-right p-v-xs">
                <form method="post" id="alert_form_<?php echo $schoolHandler->school->id; ?>" action="" url="edit_school.php?state=set_availability">
                    <i style="width:20px; height:20px;" class="zmdi zmdi-hc-2x zmdi-hc-lg <?php echo $schoolHandler->school->open == 1 ? "zmdi-close-circle" : "zmdi-check-circle" ?> btn_click_close_alertbox m-r-sm a pull-right" element_state='<?php echo $schoolHandler->school->open; ?>' element_id='<?php echo $schoolHandler->school->id; ?>' data-toggle="tooltip" title="<?= $schoolHandler->school->open == 1 ? TranslationHandler::get_static_text("CLOSE") : TranslationHandler::get_static_text("OPEN") ?>"></i>
                    <input type="hidden" name="school_id" value="<?php echo $schoolHandler->school->id; ?>">
                    <input type="hidden" name="submit" value="submit">
                </form>
            </div>
        <?php } ?>
        <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
            <div class="pull-right p-v-xs">
                <i style="width:20px; height:20px;" class="zmdi zmdi-hc-lg zmdi-hc-2x zmdi-edit m-r-xs change_page a" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT") ?>" page="edit_school" step="" args="&school_id=<?php echo $schoolHandler->school->id; ?>"></i>
            </div>
        <?php } ?>
    </div>
    <div class="profile-cover">
        <div class="cover-user m-b-lg">
            <div>
                <a <?php echo RightsHandler::has_user_right("SCHOOL_STATISTICS") ? "href='#statistic'" : "" ?> style="color: #6a6c6f !important;"><span class="cover-icon" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("STATISTICS") ?>" style="cursor:pointer"><i class="fa fa-star" style="font-size:16px !important;margin-left:2px;"></i></span></a>
            </div>
            <div>
                <div class="avatar avatar-xl">
                    <img class="img-responsive" src="assets/images/school_profile/<?php echo isset($_GET['school_id']) && !empty($schoolHandler->school->filename) ? $schoolHandler->school->filename : "default.png"; ?>" alt="avatar">
                </div>
                
            </div>
            <div class="text-center">
                <span class="cover-icon <?php echo RightsHandler::has_user_right("MAIL_WRITE_TO_SCHOOL") ? " change_page a" : " disabled" ?>" id="mail" page="mail" step="create_mail" args="&receiver_id=SCHOOL_ADMIN_<?= isset($_GET['school_id']) ? $_GET['school_id'] : ""; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>"><i class="fa fa-envelope"></i></span>
            </div>
        </div>
        <div class="text-center">
            <h4 class="profile-info-name m-b-lg"><span class="text-primary"><?= $schoolHandler->school->name ?></span></h4>
            <div class="text-muted">
                <span><?= isset($_GET['school_id']) ? $schoolHandler->school->address : ""; ?></span><br/>
                <span><?= isset($_GET['school_id']) ? $schoolHandler->school->zip_code . " " . $schoolHandler->school->city : ""; ?></span>
            </div>
        </div>
    </div>

    <div class="promo-footer">
        <div class="row no-gutter">
            <div class="col-sm-offset-4 col-sm-2 col-xs-6 promo-tab">
                <div class="text-center">
                    <h6 class="text-muted"><?php echo TranslationHandler::get_static_text("STUDENTS") . " " . strtolower(TranslationHandler::get_static_text("AND")) . " " . strtolower(TranslationHandler::get_static_text("TEACHERS")); ?></h6>
                    <h4 class="m-0 m-t-xs"><?php echo isset($_GET['school_id']) ? $schoolHandler->school->current_students . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $schoolHandler->school->max_students : ""; ?></h4>
                </div>
            </div>
            <div class="col-sm-2 promo-tab">
                <div class="text-center">
                    <h6 class="text-muted"><?php echo TranslationHandler::get_static_text("SCHOOL") . " " . strtolower(TranslationHandler::get_static_text("STATUS")); ?></h6>
                    <h4 class="m-0 m-t-xs" style="color: <?= $schoolHandler->school->open == "1" ? '#36ce1c' : '#f15530'; ?>"><?= $schoolHandler->school->open ? TranslationHandler::get_static_text("OPEN") : TranslationHandler::get_static_text("CLOSED"); ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row" id="students">
    <div class="col-sm-12">
        <div class="row">
            <?php if (RightsHandler::has_user_right("SCHOOL_FIND")) { ?>
                <div class="col-md-6">
                    <div class="panel panel-default" style="margin-left: -12px; margin-right: -6px;">
                        <div class="panel-heading">

                            <h4 class="panel-title no-transform">
                                <i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg m-r-md"></i>
                                <?php echo TranslationHandler::get_static_text("ALL") . " " . strtolower(TranslationHandler::get_static_text("STUDENTS")); ?>
                            </h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body students_list">
                            <?php if ($schoolHandler->school->current_students > 0) { ?>
                                <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                                    <thead>
                                        <tr>
                                            <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                            <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($userHandler->users as $value) {
                                            ?>
                                            <tr class = "a change_page" page = "account_profile" step = "" args = "&user_id=<?php echo $value->id; ?>">
                                                <td><?php echo $value->firstname . " " . $value->surname; ?></td>
                                                <td><?php echo $value->email; ?></td>

                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else {
                                ?>
                                
                                <div class="center description" onload="resize()">
                                    
                                    <div style="width:100%; text-align:center; margin:20px 0px;">
                                        <?php echo TranslationHandler::get_static_text("NO_STUDENTS_FOUND"); ?>
                                    </div>

                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php
            $i_max = 5;
            ?>
            <div class="col-md-6 col-sm-12">
                <div class="panel panel-default" style="margin-left: -6px; margin-right: -12px;">
                    <div class="panel-heading">
                        <h4 class="panel-title no-transform">
                            <i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg m-r-md"></i>
                            <?php echo TranslationHandler::get_static_text("TOP") . " " . $i_max . " " . strtolower(TranslationHandler::get_static_text("STUDENTS")); ?>
                        </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body students_stream">
                        <div class="streamline m-l-lg">
                            <?php
                            if (isset($_GET['school_id'])) {
                                $statisticsHandler->get_top_students($i_max, $schoolHandler->school->id);
                            }
                            
                            if(count($statisticsHandler->top_students)<1)
                            {
                                ?>
                                    <div class="streamline">
                                        <div style="width:100%; text-align:center; margin:20px 0px;"><?= TranslationHandler::get_static_text("SCHOOL_NO_ACTIVE")?></div>
                                <?php
                            }
                            else
                            {
                                ?>
                                    <div class="streamline m-l-lg">
                                <?php
                            }
                            
                            foreach ($statisticsHandler->top_students as $value) {
                                ?>
                                <div class="sl-item p-b-md sl-primary">
                                    <div class="sl-avatar avatar avatar-sm avatar-circle">
                                        <img class="img-responsive a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>" src="assets/images/profile_images/<?php echo profile_image_exists($value['profile_image']); ?>">
                                    </div>
                                    <div class="sl-content">
                                        <h5 class="m-t-0">
                                            <a class="m-r-xs text-primary a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>"><?= $value['firstname'] . ' ' . $value['surname'] ?></a>
                                            <small class="text-muted fz-sm"><?php echo $value['id'] == $classHandler->_user->id ? "<i class='zmdi zmdi-hc-lg zmdi-long-arrow-left'></i> " . TranslationHandler::get_static_text("THIS_IS_YOU") : ""; ?></small>
                                        </h5>
                                        <p><span data-plugin="counterUp"><?php echo $value['points']; ?></span> points</p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (RightsHandler::has_user_right("SCHOOL_STATISTICS")) { ?>
            <div class="row" id="statistic">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title no-transform">
                                <i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg m-r-md"></i>
                                <?php echo TranslationHandler::get_static_text("STATISTICS"); ?>
                            </h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="user-card m-b-0">
                                        <div class="center">
                                            <h4 class="widget-title p-b-md"><?php echo substr(TranslationHandler::get_static_text("AVERAGE"), 0, 10); ?></h4>
                                            <div class="pieprogress" data-value="<?php echo $statisticsHandler->school_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->school_average) ?>"}, thickness: 10}' data-size="70">
                                                <strong style="margin-top: -14px; font-size: 14px;"><span data-plugin="counterUp"><?php echo $statisticsHandler->school_average; ?></span>%    </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="user-card m-b-0">
                                        <div class="center">
                                            <h4 class="widget-title p-b-md"><?php echo TranslationHandler::get_static_text("LECTURE") . " " . strtolower(substr(TranslationHandler::get_static_text("AVERAGE"), 0, 10)); ?></h4>
                                            <div class="pieprogress" data-value="<?php echo $statisticsHandler->school_lecture_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->school_lecture_average) ?>"}, thickness: 10}' data-size="70">
                                                <strong style="margin-top: -14px; font-size: 14px;"><span data-plugin="counterUp"><?php echo $statisticsHandler->school_lecture_average; ?></span>%    </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="user-card m-b-0">
                                        <div class="center">
                                            <h4 class="widget-title p-b-md"><?php echo TranslationHandler::get_static_text("TEST") . " " . strtolower(substr(TranslationHandler::get_static_text("AVERAGE"), 0, 10)); ?></h4>
                                            <div class="pieprogress" data-value="<?php echo $statisticsHandler->school_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->school_test_average) ?>"}, thickness: 10}' data-size="70">
                                                <strong style="margin-top: -14px; font-size: 14px;"><span data-plugin="counterUp"><?php echo $statisticsHandler->school_test_average; ?></span>%    </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div id="click_close_alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div class="hidden" id="open_text"><?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("SCHOOL")) . "?"; ?></div>
        <div class="hidden" id="close_text"><?php echo TranslationHandler::get_static_text("CONFIRM_OPEN") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("SCHOOL")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_close_alertbox_btn" id="" page='school_profile' type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_close_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
                            $(document).ready(function () {
                                function resize() {
                                    if ($(".students_stream").height() > $(".students_list").height()) {
                                        var padding = Math.floor(($(".students_stream").height()) / 2);
                                        $('.description').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
                                        $(".students_list").height($(".students_stream").height());
                                    } else {
                                        $(".students_stream").height($(".students_list").height());
                                        var padding = Math.floor(($(".students_list").height()) / 2);
                                        $('.description').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
                                    }
                                }
                                $('[data-toggle="tooltip"]').tooltip();

                                $(document).on("draw.dt", resize);
                            });
</script>