<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';
require_once '../../include/handler/courseHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
$userHandler = new UserHandler();
$courseHandler = new CourseHandler();
$statisticsHandler = new StatisticsHandler();

if ($userHandler->_user->user_type_id > 1) {
    $schoolHandler->get_school_by_id($userHandler->_user->school_id);
    
    if($userHandler->_user->user_type_id > 2)
    {
        $classHandler->get_classes_by_user_id($userHandler->_user->id);
        $courseHandler->get_courses();
        $courses_average = 0;
        $courses_completed = 0;
        $course_count = count($courseHandler->courses);
        $courses_started = $course_count;
    }
}

if (isset($_GET['class_id'])) {
    $classHandler->get_class_by_id($_GET['class_id']);
    $userHandler->get_by_class_id($_GET['class_id']);
    $statisticsHandler->get_average_progress_for_class($_GET['class_id']);
}
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">
    <div class="col-md-9 col-sm-12 p-v-0">
        <div class="col-sm-12">
            <div class="widget">
                <div class='widget-header'>
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("COURSES")); ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="widget-body">
                    <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                        <thead>
                            <tr>
                                <th><?php echo TranslationHandler::get_static_text("COURSE"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("LECTURES"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("TESTS"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("PROGRESS"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($courseHandler->courses as $value) {

                                $courses_average += $value->overall_progress;

                                if($value->overall_progress > 99)
                                {
                                    $courses_completed++;
                                }
                                else if($value->overall_progress < 1)
                                {
                                    $courses_started--;
                                }
                                ?>
                                <tr class = "a change_page" page = "account_profile" step = "" args = "&user_id=<?php echo $value->id; ?>">
                                    <td><?php echo $value->title; ?></td>
                                    <td><?php echo $value->amount_of_lectures; ?></td>
                                    <td><?php echo $value->amount_of_tests; ?></td>
                                    <td><?php echo $value->overall_progress . "%"; ?></td>

                                </tr>
                            <?php }

                            $courses_average = round($courses_average / count($courseHandler->courses),0);

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-12 col-sm-12 ">
            <div class="widget">
                <div class='widget-header'>
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("STATISTICS"); ?> </h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="widget-body">
                    <div class="row">
                        <?php
                            $statisticsHandler->get_student_progress();


                        ?>
                        <div class="col-md-6 col-sm-6">
                            <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $courses_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($courses_average)?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span class="counter" data-plugin="counterUp"><?php echo $courses_average; ?></span>%    </strong>
                                        </div>
                                    </div>
                                    <div class="media-right ">
                                        <div style="margin-left: 25px;">
                                            <label  class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("COURSE")) . " ". strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></label>
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
                        
                        <div class="col-md-6 col-sm-6">
                            <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_lecture_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_lecture_average)?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span class="counter" data-plugin="counterUp"><?php echo $statisticsHandler->student_lecture_average; ?></span>%    </strong>
                                        </div>
                                    </div>
                                    <div class="media-right ">
                                        <div style="margin-left: 25px;">
                                            <label  class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("LECTURE")) . " ". strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></label>
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

                        <div class="col-md-6 col-sm-6">
                            <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_test_average)?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span class="counter" data-plugin="counterUp"><?php echo $statisticsHandler->student_test_average; ?></span>%</strong>
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
                        
                        <div class="col-md-6 col-sm-6">
                            <div class="user-card">
                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_test_average)?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 14px;"><span class="counter" data-plugin="counterUp"><?php echo $statisticsHandler->student_test_average; ?></span>%</strong>
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
                                            <?php echo TranslationHandler::get_static_text("TOTAL_POINTS") . ": " . $userHandler->_user->points;?>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--DISPLAY USER INFO!-->
    <div class="col-md-3 col-sm-12">
        <div class="widget">
            <div class="widget-header">
                <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                    <div class="pull-right">
                        <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a" page="settings" step="" args="&user_id=<?php echo $userHandler->_user->id; ?>"></i>
                    </div>
                <?php } ?>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PROFILE");?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("NAME") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname; ?></span>
                </div>
                
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USERNAME") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->username; ?></span>
                </div>
                
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("INFO_EMAIL") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->email; ?></span>
                </div>
                
                <?php
                if($userHandler->_user->user_type_id > 1)
                {
                ?>
                    <div>
                        <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("SCHOOL") . ":"; ?></label>
                        <a class="change_page" page="school_profile" step="" args="&school_id=<?php echo $schoolHandler->school->id ?>" href="javascript:void(0)">
                            <span class="pull-right"><?php echo $schoolHandler->school->name; ?></span>
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        
        <!--DISPLAY CLASSES!-->
        <?php
        if($userHandler->_user->user_type_id > 2)
            {
                if(count($classHandler->classes) > 0)
                {
                ?>
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CLASSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <?php

                                    for($i=0; $i < count($classHandler->classes); $i++)
                                    {
                                        echo '<div><a class="change_page" page="class_profile" step="" args="&class_id=' . $classHandler->classes[$i]->id . '" href="javascript:void(0)">' . $classHandler->classes[$i]->title . '</a></div>';
                                    }

                            ?>
                        </div>
                    </div>
        
                <?php
                }
            }
        ?>
        
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>