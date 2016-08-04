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
                                ?>
                                <tr class = "a change_page" page = "account_profile" step = "" args = "&user_id=<?php echo $value->id; ?>">
                                    <td><?php echo $value->title; ?></td>
                                    <td><?php echo $value->amount_of_lectures; ?></td>
                                    <td><?php echo $value->amount_of_tests; ?></td>
                                    <td><?php echo $value->overall_progress . "%"; ?></td>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        $i_max = 10;
        $i = 0;
        $i_rand = rand(100, 1000);
        ?>
        
        <div class="col-md-12 col-sm-12 ">
            <div class="widget">
                <div class='widget-header'>
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("STATISTICS"); ?> </h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="widget-body">
                    <div class></div>
                        <?php
                            $statisticsHandler->get_average_progress_for_student();
                            ?>
                        <div class="col-md-4 col-sm-4">

                            <div class="user-card p-md">

                                    <div class="media-left">
                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_lecture_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_lecture_average)?>"}, thickness: 10}' data-size="70">
                                            <strong style="margin-top: -14px; font-size: 16px;">%<span class="counter" data-plugin="counterUp"><?php echo $statisticsHandler->student_lecture_average; ?></span></strong>
                                        </div>
                                    </div>
                                    <div class="media-right">
                                        <h5 class="media-heading"><a href="javascript:void(0)" class="title-color user_full_name"><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname; ?></a></h5>
                                        <small class="media-meta"><?php echo $userHandler->_user->user_type_title; ?></small>
                                    </div>

                            </div>


                            <div class="pull-left" name="test_average">
                                
                            </div>
                            <div class="pull-right">
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USERNAME") . ":"; ?></label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="streamline m-l-lg">
                                <div class="pull-left" name="test_average">
                                    <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_test_average)?>"}, thickness: 10}'>
                                        <strong>%<span class="counter" data-plugin="counterUp"><?php echo $statisticsHandler->student_test_average; ?></span></strong>
                                    </div>
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USERNAME") . ":"; ?></label>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
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
                        <span class="pull-right"><?php echo $schoolHandler->school->name; ?></span>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        
        
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
                                        echo '<div>' . $classHandler->classes[$i]->title . '</div>';
                                    }

                            ?>
                        </div>
                    </div>
        
                <?php
                }
            }
        ?>
        <div class="widget">
            <div class='widget-header'>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <div class="pull-left" name="test_average">
                    <div class="pieprogress" data-value="<?php echo isset($_GET['class_id']) ? $statisticsHandler->class_average : ""; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo isset($_GET['class_id']) ? get_progress_color($statisticsHandler->class_average * 100) : "" ?>"}, thickness: 10}'>
                        <strong>%<span class="counter" data-plugin="counterUp"><?php echo isset($_GET['class_id']) ? $statisticsHandler->class_average * 100 : ""; ?></span></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget">
            <div class='widget-header'>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("HOMEWORK"); ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <?php
                $end = microtime(true);

                echo '<br/><br/>Time spent: <strong style="font-size: 40px;">' . floor(($end - $begin) * 1000) . '</strong>ms';
                ?>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>