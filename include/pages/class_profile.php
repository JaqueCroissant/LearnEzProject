<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
$userHandler = new UserHandler();
$statisticsHandler = new StatisticsHandler();
$homeworkHandler = new HomeworkHandler();

if ($classHandler->_user->user_type_id != 1) {
    $schoolHandler->get_school_by_id($classHandler->_user->school_id);
}

if (isset($_GET['class_id'])) {
    if (!$classHandler->get_class_by_id($_GET['class_id'])) {
        ErrorHandler::show_error_page($classHandler->error);
        die();
    }
    $userHandler->get_by_class_id($_GET['class_id'], true);
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
                                                    <td><?php echo htmlspecialchars($value->firstname) . " " . htmlspecialchars($value->surname); ?></td>
                                                    <td><?php echo htmlspecialchars($value->email); ?></td>

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
                                                    <td><?php echo htmlspecialchars($value->firstname) . " " . htmlspecialchars($value->surname); ?></td>
                                                    <td><?php echo htmlspecialchars($value->email); ?></td>

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
            <?php
        }
        if ($classHandler->_user->user_type_id != 1) {
            ?>
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class='panel-heading'>
                        <h4 class="panel-title no-transform">
                            <i class="zmdi-hc-fw zmdi zmdi-assignment zmdi-hc-lg m-r-md"></i>
                            <?php echo TranslationHandler::get_static_text("HOMEWORK"); ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <?php
                        if (isset($_GET['class_id'])) {
                            $homeworkHandler->get_class_homework($_GET['class_id']);
                            if (empty($homeworkHandler->homework)) {
                                ?>
                                <div class="center latest-homework-empty m-h-md"><?php echo TranslationHandler::get_static_text("CLASS_NO_HOMEWORK_AT_THE_MOMENT"); ?></div>
                            <?php } else { ?>


                                <div class="latest-homework">
                                    <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json" : "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                        <thead>
                                            <tr>
                                                <th><?php echo TranslationHandler::get_static_text("TITLE") ?></th>
                                                <th><?php echo TranslationHandler::get_static_text("CLASSES") ?></th>
                                                <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("END") . " " . TranslationHandler::get_static_text("DATE_DATE") ?></th>
                                                <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("LECTURES") ?></th>
                                                <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("TESTS") ?></th>
                                                <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("STATUS") ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($homeworkHandler->homework as $value) {
                                                $classes = "";
                                                for ($i = 0; $i < count($value->classes); $i++) {
                                                    $classes .= htmlspecialchars($value->classes[$i]->title);
                                                    $classes .= $i != count($value->classes) - 1 ? ", " : "";
                                                }
                                                ?>
                                                <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                <?php
                                                if (!empty($value->lectures)) {
                                                    echo '<b>Lektioner:</b>';
                                                    foreach ($value->lectures as $lecture) {
                                                        echo '<br />- ' . htmlspecialchars($lecture->title) . '';
                                                    }
                                                    echo '<br />';
                                                }

                                                if (!empty($value->tests)) {
                                                    echo '<b>Tests:</b>';
                                                    foreach ($value->tests as $test) {
                                                        echo '<br />- ' . htmlspecialchars($test->title) . '';
                                                    }
                                                }
                                                ?>">
                                                    <td><?php echo $value->title; ?></td>
                                                    <td><span data-toggle="tooltip" title="<?= htmlspecialchars($classes) ?>"><?= strlen(htmlspecialchars($classes)) > 30 ? substr(htmlspecialchars($classes), 0, 30) . "..." : htmlspecialchars($classes) ?></span></td>
                                                    <td style="text-align: center;"><?php echo $value->date_expire; ?></td>
                                                    <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                                    <td style='text-align:center;'><?= count($value->tests) ?></td>
                                                    <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="Ufuldendt"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="Udført"></i>' ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php }
                            ?>

                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-9">
                        <h4 class="panel-title no-transform">
                            <i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg m-r-md"></i>
                            <?php echo isset($_GET['class_id']) ? htmlspecialchars($classHandler->school_class->title) . " - " . htmlspecialchars($classHandler->school_class->class_year) : ""; ?>
                        </h4>
                    </div>
                    <div class="col-md-3">
                        <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                            <div class="pull-right">
                                <form class="" style="display: inline-block;" method="post" id="click_alert_form_<?php echo $classHandler->school_class->id; ?>" url="edit_class.php?state=delete_class">
                                    <i style="width:20px; height:20px;" class="zmdi zmdi-hc-lg zmdi-delete btn_click_alertbox a" element_id='<?php echo $classHandler->school_class->id; ?>' data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("DELETE") ?>"></i>
                                    <input type="hidden" name="class_id" value="<?php echo $classHandler->school_class->id; ?>">
                                    <input type="hidden" name="submit" value="submit"></input>
                                </form>
                            </div>
                        <?php } ?>
                        <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                            <div class="pull-right">
                                <form method="post" id="alert_form_<?php echo $classHandler->school_class->id; ?>" action="" url="edit_class.php?state=set_availability">
                                    <i style="width:20px; height:20px; margin-top: 3px;" class="zmdi zmdi-hc-lg <?php echo $classHandler->school_class->open == 1 ? "zmdi-close-circle" : "zmdi-check-circle" ?> btn_click_close_alertbox m-r-sm a pull-right" element_state='<?php echo $classHandler->school_class->open; ?>' element_id='<?php echo $classHandler->school_class->id; ?>' data-toggle="tooltip" title="<?= $classHandler->school_class->open == 1 ? TranslationHandler::get_static_text("CLOSE") : TranslationHandler::get_static_text("OPEN") ?>"></i>
                                    <input type="hidden" name="class_id" value="<?php echo $classHandler->school_class->id; ?>">
                                    <input type="hidden" name="submit" value="submit">
                                </form>
                            </div>
                        <?php } ?>
                        <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                            <div class="pull-right">
                                <i style="width:20px; height:20px;" class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT") ?>" page="edit_class" step="" args="&class_id=<?php echo $classHandler->school_class->id; ?>"></i>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <?php echo isset($_GET['class_id']) ? htmlspecialchars($classHandler->school_class->description) : ""; ?>
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
                                    <a class="m-r-xs text-primary a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>"><?= ucwords(htmlspecialchars($value['firstname']) . ' ' . htmlspecialchars($value['surname'])) ?></a>
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




<div id="click_close_alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div class="hidden" id="open_text"><?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("CLASS")) . "?"; ?></div>
        <div class="hidden" id="close_text"><?php echo TranslationHandler::get_static_text("CONFIRM_OPEN") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("CLASS")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_close_alertbox_btn" id="" page='class_profile' type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_close_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<div id="click_alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div id="delete_text"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("CLASS")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_alertbox_btn" id="profile_accept_delete" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>