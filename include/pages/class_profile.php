<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
$userHandler = new UserHandler();
$statisticsHandler = new StatisticsHandler();

if ($classHandler->_user->user_type_id != 1) {
    $schoolHandler->get_school_by_id($classHandler->_user->school_id);
}

if (isset($_GET['class_id'])) {
    $classHandler->get_class_by_id($_GET['class_id']);
    $userHandler->get_by_class_id($_GET['class_id']);
    $statisticsHandler->get_average_progress_for_class($_GET['class_id']);
} else {
    ErrorHandler::show_error_page(ErrorHandler::return_error("USER_INVALID_CLASS_ID"));
}
$i_max = 5;
$i = 0;
$i_rand = rand(100, 1000);
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">
    <div class="col-md-9 col-sm-12 p-v-0">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="m-b-lg nav-tabs-horizontal">
                    <ul class="nav nav-tabs" role="tablist">
                        <li id="student_tab_header"><a href="#student_tab" class="my_tab_header" id="student_tab_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></a></li>
                        <li id="teacher_tab_header"><a href="#teacher_tab" class="my_tab_header" id="teacher_tab_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("TEACHERS"); ?></a></li>
                    </ul>
                    <div class="my_tab_content">
                        <div class="my_fade my_tab" id="student_tab">
                            <div class="panel-body">
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
                                            if ($value->user_type_id == 4) {
                                                ?>
                                                <tr class="a change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>">
                                                    <td><?php echo $value->firstname . " " . $value->surname; ?></td>
                                                    <td><?php echo $value->email; ?></td>

                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="my_fade my_tab" id="teacher_tab">
                            <div class="panel-body">
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
                                            if ($value->user_type_id == 3) {
                                                ?>
                                                <tr class="a change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>">
                                                    <td><?php echo $value->firstname . " " . $value->surname; ?></td>
                                                    <td><?php echo $value->email; ?></td>

                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ?>
        <?php if (RightsHandler::has_user_right("CLASS_STATISTICS")) { ?>
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
                                        <h4 class="panel-title no-transform p-b-md"><?php echo substr(TranslationHandler::get_static_text("AVERAGE"), 0, 10); ?></h4>
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->class_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->class_average) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><?php echo $statisticsHandler->class_average; ?>%    </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="user-card m-b-0">
                                    <div class="center">
                                        <h4 class="panel-title no-transform p-b-md"><?php echo TranslationHandler::get_static_text("LECTURE") . " " . strtolower(substr(TranslationHandler::get_static_text("AVERAGE"), 0, 10)); ?></h4>
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->class_lecture_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->class_lecture_average) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><?php echo $statisticsHandler->class_lecture_average; ?>%    </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="user-card m-b-0">
                                    <div class="center">
                                        <h4 class="panel-title no-transform p-b-md"><?php echo TranslationHandler::get_static_text("TEST") . " " . strtolower(substr(TranslationHandler::get_static_text("AVERAGE"), 0, 10)); ?></h4>
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->class_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->class_test_average) ?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><?php echo $statisticsHandler->class_test_average; ?>%    </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="col-sm-12">
            <div class="panel panel-default accordion">
                <div class='panel-heading'>
                    <a class="accordion-toggle" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="false" aria-controls="collapse-1">
                        <label><?php echo TranslationHandler::get_static_text("HOMEWORK"); ?></label>
                        <i class="fa acc-switch"></i>
                    </a>
                </div>
                <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1" aria-expanded="false">
                    <div class="panel-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="panel-title no-transform">
                            <i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg m-r-md"></i>
                            <?php echo isset($_GET['class_id']) ? $classHandler->school_class->title . " - " . $classHandler->school_class->class_year : ""; ?>
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                            <i class="zmdi zmdi-hc-lg zmdi-delete change_page a pull-right" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("DELETE") ?>" page="edit_class" step="" args="&class_id=<?php echo $classHandler->school_class->id; ?>"></i>
                        <?php } ?>
                        <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                            <i class="zmdi zmdi-hc-lg zmdi-close-circle m-r-sm change_page a pull-right" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("CLOSE") ?>" page="edit_class" step="" args="&class_id=<?php echo $classHandler->school_class->id; ?>"></i>
                        <?php } ?>
                        <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                            <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a pull-right" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT") ?>" page="edit_class" step="" args="&class_id=<?php echo $classHandler->school_class->id; ?>"></i>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <?php echo isset($_GET['class_id']) ? $classHandler->school_class->description : ""; ?>
            </div>
            <div class="panel-body <?php echo isset($_GET['class_id']) && $classHandler->school_class->remaining_days < "21" ? "danger animated headShake" : "hidden"; ?>">
                <h4 class="panel-title no-transform <?php echo isset($_GET['class_id']) && $classHandler->school_class->remaining_days < "21" ? "animated flash animate-twice" : ""; ?>"><?php echo isset($_GET['class_id']) ? TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END") . ": " . $classHandler->school_class->remaining_days . " " . TranslationHandler::get_static_text("DATE_DAYS") : ""; ?></h4>
            </div>
        </div>
        <div class="panel panel-default">
            <div class='panel-heading'>
                <h4 class="panel-title no-transform">
                    <i class="zmdi-hc-fw zmdi zmdi-calendar-note zmdi-hc-lg m-r-md"></i>
                    <?php echo TranslationHandler::get_static_text("DAYS_REMAINING") . ": " . $classHandler->school_class->remaining_days; ?>
                </h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <div class="pull-left" name="test_average">
                    <div class="pieprogress" data-value="<?php echo isset($_GET['class_id']) ? $classHandler->school_class->remaining_days / $classHandler->school_class->total_days : ""; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo isset($_GET['class_id']) ? get_progress_color($classHandler->school_class->remaining_days * 100 / $classHandler->school_class->total_days) : "" ?>"}, thickness: 10}'>
                        <strong><?php echo isset($_GET['class_id']) ? round($classHandler->school_class->remaining_days * 100 / $classHandler->school_class->total_days, 0) : ""; ?> %</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class='panel-heading'>
                <h4 class="panel-title no-transform">
                    <i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg m-r-md"></i>
                    <?php echo TranslationHandler::get_static_text("TOP") . " " . $i_max . " " . strtolower(TranslationHandler::get_static_text("STUDENTS")); ?> 
                </h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <div class="streamline m-l-lg">
                    <?php
                    if (isset($_GET['class_id'])) {
                        $statisticsHandler->get_top_students($i_max, null, $_GET['class_id']);
                    }
                    foreach ($statisticsHandler->top_students as $value) {
                        ?>
                        <div class="sl-item p-b-md sl-primary">
                            <div class="sl-avatar avatar avatar-sm avatar-circle">
                                <img class="img-responsive a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>" src="assets/images/profile_images/<?php echo $value['image_id']; ?>.png">
                            </div>
                            <div class="sl-content">
                                <h5 class="m-t-0">
                                    <a class="m-r-xs text-primary a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>"><?= $value['firstname'] . ' ' . $value['surname'] ?></a>
                                    <small class="text-muted fz-sm"><?php echo $value['id'] == $classHandler->_user->id ? "<i class='zmdi zmdi-hc-lg zmdi-long-arrow-left'></i> " . TranslationHandler::get_static_text("THIS_IS_YOU") : ""; ?></small>
                                </h5>
                                <p><?php echo $value['points']; ?> points</p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>