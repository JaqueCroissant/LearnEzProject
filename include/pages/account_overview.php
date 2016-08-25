<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';
require_once '../../include/handler/courseHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$userHandler = new UserHandler();
$courseHandler = new CourseHandler();
$statisticsHandler = new StatisticsHandler();
$homeworkHandler = new HomeworkHandler();

$courses_average = 0;
$courses_completed = 0;
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">

    <?php
    switch ($userHandler->_user->user_type_id) {
        case "1":

            $schoolHandler->get_all_schools();
            $schoolHandler->get_soon_expiring_schools();
            $statisticsHandler->get_top_students();
            $statisticsHandler->get_completion_stats(7);
            ?>

            <div class="col-md-9 p-v-0">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("SCHOOL_FIND") ? ' a change_page" page="find_school" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_SCHOOL") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("SCHOOLS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false, targets: [4]}]}">
                                <thead>
                                    <tr>
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("CITY"); ?></th>


                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STATUS"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($schoolHandler->all_schools) > 0) {
                                        foreach ($schoolHandler->all_schools as $value) {
                                            ?>
                                            <tr class = "a change_page" page="school_profile" step = "" args = "&school_id=<?php echo $value->id; ?>">
                                                <td><?php echo (strlen($value->name) > 20 ? htmlspecialchars(substr($value->name, 0, 20)) : htmlspecialchars($value->name)); ?></td>
                                                <td><?php echo (strlen($value->address) > 20 ? htmlspecialchars(substr($value->address, 0, 20)) : htmlspecialchars($value->address)); ?></td>
                                                <td><?php echo htmlspecialchars($value->zip_code); ?></td>
                                                <td><?php echo (strlen($value->city) > 15 ? htmlspecialchars(substr($value->city, 0, 15)) : htmlspecialchars($value->city)); ?></td>
                                                <td style="text-align: center;"><?php echo!$value->open ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("CLOSED") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("OPEN") . '"></i>'; ?></td>
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

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class='panel-heading p-h-lg p-v-md'>
                                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("TOP") . " " . strtolower(TranslationHandler::get_static_text("STUDENTS")); ?> </h4>
                                </div>
                                <hr class="widget-separator m-0">
                                <div class="widget-body">
                                    
                                        <?php
                                        if(count($statisticsHandler->top_students) < 1)
                                        {
                                            ?>
                                            <div class="streamline">
                                                <div style="width:100%; text-align:center; margin:20px 0px;"><?= TranslationHandler::get_static_text("NO_STUDENTS_FOUND")?></div>
                                            <?php
                                        }
                                        else
                                        {?>
                                                <div class="streamline m-l-lg">
                                                <?php
                                            foreach($statisticsHandler->top_students as $value)
                                            { ?>
                                                <div class="sl-item p-b-md">
                                                    <div class="sl-avatar avatar avatar-sm avatar-circle">
                                                        <img class="img-responsive" src="<?php echo "assets/images/profile_images/" . profile_image_exists($value['profile_image']) ?>">
                                                    </div>
                                                    <div class="sl-content">
                                                        <h5 class="m-t-0">
                                                            <a class="m-r-xs text-primary a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>"><?php echo htmlspecialchars($value['firstname']) . " " . htmlspecialchars($value['surname'])?></a>
                                                            <small class="text-muted fz-sm"><?php echo htmlspecialchars($value['name']) ?></small>
                                                        </h5>
                                                        <p><?php echo $value['points'] . " " . strtolower(TranslationHandler::get_static_text("POINTS")); ?></p>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    

                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading p-h-lg p-v-md" >
                                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("COURSE_PROGRESS") ?></h4>
                                </div>
                                <hr class="widget-separator m-0">
                                <div class="panel-body user-progress">
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
                                         for ($i = date('w') - 6; $i < date('w') + 1; $i++) {
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
                                         data:
                                         [
                                         <?php
                                         for ($i = date('j') - 6; $i < date('j') + 1; $i++) {

                                             $num = $i > 0 ? $i : date("t", strtotime(date("Y-m-d -1 months"))) + $i;
                                             if (array_key_exists($num, $statisticsHandler->global_lectures_complete)) {
                                                 echo $statisticsHandler->global_lectures_complete[$num];
                                             } else {
                                                 echo "0";
                                             }

                                             if ($i != date('j')) {
                                                 echo ",";
                                             }
                                         }
                                         ?>
                                         ]
                                         },
                                         {
                                         name:'<?= TranslationHandler::get_static_text("TESTS") ?>',
                                         type:'line',
                                         smooth:true,
                                         itemStyle: {normal: {areaStyle: {type: 'default'}}},
                                         data:[
                                         <?php
                                         for ($i = date('j') - 6; $i < date('j') + 1; $i++) {

                                             $num = $i > 0 ? $i : date("t", strtotime(date("Y-m-d -1 months"))) + $i;
                                             if (array_key_exists($num, $statisticsHandler->global_tests_complete)) {
                                                 echo $statisticsHandler->global_tests_complete[$num];
                                             } else {
                                                 echo "0";
                                             }

                                             if ($i != date('j')) {
                                                 echo ",";
                                             }
                                         }
                                         ?>
                                         ]
                                         }
                                         ]
                                         }" style="height: 300px;"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <?php
            break;

        //LOKAL ADMIN DASHBOARD
        case "2":
            $homeworkHandler->get_specific_user_homework($userHandler->_user->id);
            $schoolHandler->get_school_by_id($userHandler->_user->school_id);
            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
            $userHandler->get_by_school_id($userHandler->_user->school_id);
            ?>

            <div class="col-md-9 p-v-0">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("CLASS_FIND") ? ' a change_page" page="find_class" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_CLASS") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("CLASSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false, targets: [3]}]}">
                                <thead>
                                    <tr>
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STATUS"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($classHandler->classes) > 0) {
                                        foreach ($classHandler->classes as $value) {
                                            ?>
                                            <tr class = "a change_page" page="class_profile" step = "" args = "&class_id=<?php echo $value->id; ?>">
                                                <td><?php echo htmlspecialchars($value->title); ?></td>
                                                <td style="text-align: center;"><?php echo htmlspecialchars($value->class_year); ?></td>
                                                <td style="text-align: center;"><?php echo htmlspecialchars($value->number_of_students); ?></td>
                                                <td style="text-align: center;"><?php echo!$value->open ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("CLOSED") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("OPEN") . '"></i>'; ?></td>
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


                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("ACCOUNT_FIND") ? ' a change_page" page="find_account" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_ACCOUNT") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("ACCOUNTS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                                <thead>
                                    <tr>
                                        <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($userHandler->users) > 0) {
                                        foreach ($userHandler->users as $value) {
                                            ?>
                                            <tr class = "a change_page" page="account_profile" step = "" args = "&user_id=<?php echo $value->id; ?>">
                                                <td><?php echo htmlspecialchars($value->username); ?></td>
                                                <td><?php echo (strlen($value->firstname . " " . $value->surname) > 20 ? htmlspecialchars(substr($value->firstname . " " . $value->surname, 0, 20)) : htmlspecialchars($value->firstname . " " . $value->surname)); ?></td>
                                                <td><?php echo $value->user_type_title; ?></td>
                                                <td><?php echo strlen($value->email) > 20 ? htmlspecialchars(substr($value->email, 0, 20)) : htmlspecialchars($value->email); ?></td>
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


                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_page_right("HOMEWORK_OVERVIEW") ? ' a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("HOMEWORK_OVERVIEW") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-assignment-o zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="panel-body">
                                <?php
                                if (empty($homeworkHandler->homework)) {
                                    echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> ' . TranslationHandler::get_static_text("NO_HOMEWORK") . '</div>';
                                } else {
                                    ?>
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
                                                        $classes .= $value->classes[$i]->title;
                                                        $classes .= $i != count($value->classes) - 1 ? ", " : "";
                                                    }
                                                    ?>
                                                    <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                    <?php
                                                    if (!empty($value->lectures)) {
                                                        echo TranslationHandler::get_static_text("LECTURES") . ":";
                                                        foreach ($value->lectures as $lecture) {
                                                            echo '<br />- ' . htmlspecialchars($lecture->title) . '';
                                                        }
                                                        echo '<br />';
                                                    }

                                                    if (!empty($value->tests)) {
                                                        echo TranslationHandler::get_static_text("TESTS") . ":";
                                                        foreach ($value->tests as $test) {
                                                            echo '<br />- ' . htmlspecialchars($test->title) . '';
                                                        }
                                                    }
                                                    ?>">
                                                        <td><?php echo htmlspecialchars($value->title); ?></td>
                                                        <td><span data-toggle="tooltip" title="<?= htmlspecialchars($classes) ?>"><?= strlen($classes) > 25 ? htmlspecialchars(substr($classes, 0, 25)) . "..." : htmlspecialchars($classes) ?></span></td>
                                                        <td style="text-align: center;"><?php echo $value->date_expire; ?></td>
                                                        <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                                        <td style='text-align:center;'><?= count($value->tests) ?></td>
                                                        <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("INCOMPLETE") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("COMPLETE") . '"></i>' ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            break;

        case "3":
            //TEACHER DASHBOARD

            $schoolHandler->get_school_by_id($userHandler->_user->school_id);
            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
            $homeworkHandler->get_specific_user_homework($userHandler->_user->id);
            $classHandler->get_classes_by_user_id($userHandler->_user->id);
            $courseHandler->get_courses();
            $course_count = count($courseHandler->courses);
            $courses_started = $course_count;
            ?>
            <div class="col-md-9 p-v-0">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("CLASS_FIND") ? ' a change_page" page="find_class" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_CLASS") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false, targets: [3]}]}">
                                <thead>
                                    <tr>
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STATUS"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($classHandler->classes) > 0) {
                                        foreach ($classHandler->classes as $value) {
                                            ?>
                                            <tr class = "a change_page" page="class_profile" step = "" args = "&class_id=<?php echo $value->id; ?>">
                                                <td><?php echo htmlspecialchars($value->title); ?></td>
                                                <td style="text-align: center;"><?php echo htmlspecialchars($value->class_year); ?></td>
                                                <td style="text-align: center;"><?php echo htmlspecialchars($value->number_of_students); ?></td>
                                                <td style="text-align: center;"><?php echo!$value->open ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("CLOSED") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("OPEN") . '"></i>'; ?></td>
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


                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_page_right("HOMEWORK_OVERVIEW") ? ' a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("HOMEWORK_OVERVIEW") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-assignment-o zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="panel-body">
                                <?php
                                if (empty($homeworkHandler->homework)) {
                                    echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;">' . TranslationHandler::get_static_text("NO_HOMEWORK") . '</div>';
                                } else {
                                    ?>
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
                                                        $classes .= $value->classes[$i]->title;
                                                        $classes .= $i != count($value->classes) - 1 ? ", " : "";
                                                    }
                                                    ?>
                                                    <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                    <?php
                                                    if (!empty($value->lectures)) {
                                                        echo '<b>' . TranslationHandler::get_static_text("LECTURES") . '</b>';
                                                        foreach ($value->lectures as $lecture) {
                                                            echo '<br />- ' . htmlspecialchars($lecture->title) . '';
                                                        }
                                                        echo '<br />';
                                                    }

                                                    if (!empty($value->tests)) {
                                                        echo '<b>' . TranslationHandler::get_static_text("TESTS") . '</b>';
                                                        foreach ($value->tests as $test) {
                                                            echo '<br />- ' . htmlspecialchars($test->title) . '';
                                                        }
                                                    }
                                                    ?>">
                                                        <td><?php echo htmlspecialchars($value->title); ?></td>
                                                        <td><span data-toggle="tooltip" title="<?= htmlspecialchars($classes) ?>"><?= strlen($classes) > 30 ? htmlspecialchars(substr($classes, 0, 30)) . "..." : htmlspecialchars($classes) ?></span></td>
                                                        <td style="text-align: center;"><?php echo $value->date_expire; ?></td>
                                                        <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                                        <td style='text-align:center;'><?= count($value->tests) ?></td>
                                                        <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("INCOMPLETE") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("COMPLETE") . '"></i>' ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            break;

        //STUDENT DASHBOARD    
        case "4":

            $schoolHandler->get_school_by_id($userHandler->_user->school_id);
            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
            $homeworkHandler->get_user_homework();
            $classHandler->get_classes_by_user_id($userHandler->_user->id);
            $courseHandler->get_courses();
            $course_count = count($courseHandler->courses);
            $courses_started = $course_count;
            $statisticsHandler->get_student_stats();
            ?>
            <div class="col-md-9 p-v-0">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_page_right("COURSE_OVERVIEW") ? ' a change_page" page="course_overview" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("COURSE_OVERVIEW") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-graduation-cap zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("COURSES")); ?></h4>
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
                                        if ($value->overall_progress > 99) {
                                            $courses_completed++;
                                        } else if ($value->overall_progress < 1) {
                                            $courses_started--;
                                        }
                                        ?>
                                        <tr class = "a change_page" page="course_show" step = "" args = "&course_id=<?php echo $value->id; ?>">
                                            <td><?php echo htmlspecialchars($value->title); ?></td>
                                            <td><?php echo htmlspecialchars($value->amount_of_lectures); ?></td>
                                            <td><?php echo htmlspecialchars($value->amount_of_tests); ?></td>
                                            <td><?php echo htmlspecialchars($value->overall_progress); ?>%</td>
                                        </tr>
                                        <?php
                                    }
                                    $courses_average = $course_count > 0 ? round($courses_average / $course_count, 0) : 0;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_page_right("HOMEWORK_OVERVIEW") ? ' a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("HOMEWORK_OVERVIEW") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-assignment-o zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="panel-body">
                                <?php
                                if (empty($homeworkHandler->homework)) {
                                    echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;">' . TranslationHandler::get_static_text("NO_HOMEWORK") . '</div>';
                                } else {
                                    ?>
                                    <div class="latest-homework">
                                        <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json" : "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                            <thead>
                                                <tr>
                                                    <th><?= TranslationHandler::get_static_text("TITLE") ?></th>
                                                    <th><?= TranslationHandler::get_static_text("CLASSES") ?></th>
                                                    <th><?= TranslationHandler::get_static_text("END") . " " . strtolower(TranslationHandler::get_static_text("DATE_DATE")) ?></th>
                                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("LECTURES") ?></th>
                                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("TESTS") ?></th>
                                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("STATUS") ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($homeworkHandler->homework as $value) {
                                                    $classes = "";
                                                    for ($i = 0; $i < count($value->classes); $i++) {
                                                        $classes .= $value->classes[$i]->title;
                                                        $classes .= $i != count($value->classes) - 1 ? ", " : "";
                                                    }
                                                    ?>
                                                    <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                    <?php
                                                    if (!empty($value->lectures)) {
                                                        echo '<b>' . TranslationHandler::get_static_text("LECTURES") . ":" . '</b>';
                                                        foreach ($value->lectures as $lecture) {
                                                            echo '<br />- ' . htmlspecialchars($lecture->title) . '';
                                                        }
                                                        echo '<br />';
                                                    }

                                                    if (!empty($value->tests)) {
                                                        echo '<b>' . TranslationHandler::get_static_text("TESTS") . ":" . '</b>';
                                                        foreach ($value->tests as $test) {
                                                            echo '<br />- ' . htmlspecialchars($test->title) . '';
                                                        }
                                                    }
                                                    ?>">
                                                        <td><?php echo htmlspecialchars($value->title); ?></td>
                                                        <td><span data-toggle="tooltip" title="<?= htmlspecialchars($classes) ?>"><?= strlen($classes) > 35 ? htmlspecialchars(substr($classes, 0, 35)) . "..." : htmlspecialchars($classes) ?></span></td>
                                                        <td><?php echo $value->date_expire; ?></td>
                                                        <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                                        <td style='text-align:center;'><?= count($value->tests) ?></td>
                                                        <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("INCOMPLETE") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("COMPLETE") . '"></i>' ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">

                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("STATISTICS"); ?> </h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">

                            <div class="row">
                                <div class="col-md-6">
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

                                <div class="col-md-6">
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

                                <div class="col-md-6">
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
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php
            break;
    }
    ?>


    <!--DISPLAY USER INFO!-->
    <div class="col-md-3">
        <div class="panel">
            <div class="panel panel-default">
                <div class='panel-heading p-h-lg p-v-md'>
                    <h4 class="panel-title a change_page" page="account_profile" args="&user_id=<?= $userHandler->_user->id ?>" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("PROFILE") ?>" style="text-transform: none !important;">
                        <i class="zmdi-hc-fw zmdi zmdi-graduation-cap zmdi-hc-lg" style="padding-right:30px;"></i>
                        <?php echo TranslationHandler::get_static_text("PROFILE"); ?>
                        <?php if (RightsHandler::has_page_right("SETTINGS_EDIT_USER_INFO")) { ?>
                            <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a pull-right" page="settings" step="edit_user_info" args="&user_id=<?php echo $userHandler->_user->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT_ACCOUNT") ?>"></i>
                        <?php } ?>
                    </h4>
                </div>
                <div style="clear:both;"></div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <div style="width:100%;">
                <div class="avatar avatar-circle" style="display:block !important;margin: 10px auto 10px auto !important; width: 100px; height: 100px;">
                    <img src="assets/images/profile_images/uncropped/<?= profile_image_exists($userHandler->_user->profile_image); ?>" alt="avatar">
                </div>
                </div>
                <div class="center">
                    <b><?php echo strlen(htmlspecialchars($userHandler->_user->firstname . " " . $userHandler->_user->surname)) > 30 ? substr(htmlspecialchars($userHandler->_user->firstname . " " . $userHandler->_user->surname), 0, 30) . ".." : htmlspecialchars($userHandler->_user->firstname . " " . $userHandler->_user->surname); ?></b>
                </div>
                <br/>
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USERNAME") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->username; ?></span>
                </div>
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USER_TYPE") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->user_type_title; ?></span>
                </div>
                <?php
                    if($userHandler->_user->user_type_id==4)
                    {
                    ?>
                        <div>
                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("POINTS") . ":"; ?></label>
                            <span class="pull-right"><?php echo $userHandler->_user->points; ?></span>
                        </div>
                        <?php
                    }
                    ?>
                    <div>
                        <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("OS") . ":"; ?></label>
                        <span class="pull-right">
                            <?php
                            $os_data = $courseHandler::get_os_options();
                            foreach ($os_data as $os) {
                                if ($os['id'] == $userHandler->_user->settings->os_id) {
                                    echo $os['title'];
                                }
                            }
                            ?>
                        </span>
                    </div>
                    <div>
                        <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("INFO_EMAIL") . ":"; ?></label>
                        <span class="pull-right"><?php echo htmlspecialchars($userHandler->_user->email); ?></span>
                    </div>

                    <?php
                    if ($userHandler->_user->user_type_id > 1) {
                        ?>
                        <div>
                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("SCHOOL") . ":"; ?></label>

                            <a class="change_page" page="school_profile" step="" args="&school_id=<?php echo $schoolHandler->school->id ?>" href="javascript:void(0)">
                                <span class="pull-right"><?php echo htmlspecialchars($schoolHandler->school->name); ?></span>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>




        <?php
        if ($userHandler->_user->user_type_id > 3) {
            if (count($classHandler->classes) > 0) {
                ?>
                <div class="panel">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("CLASSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <?php
                            for ($i = 0; $i < count($classHandler->classes); $i++) {
                                echo '<div><a class="change_page" page="class_profile" step="" args="&class_id=' . $classHandler->classes[$i]->id . '" href="javascript:void(0)">' . htmlspecialchars($classHandler->classes[$i]->title) . '</a></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <?php
            }
        }

        if ($userHandler->_user->user_type_id == 1) {
            ?>
            <div class="row">
                <div class="col-md-12 col-md-12">
                    <div class="panel panel-default">
                        <div class='panel-heading'>
                            <h4 class="panel-title no-transform">
                                <i class="zmdi-hc-fw zmdi zmdi-calendar-note zmdi-hc-lg m-r-md"></i>
                                <?php echo TranslationHandler::get_static_text("SOON_EXPIRING"); ?>
                            </h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body">
                            <div class="streamline m-l-lg">
                                <?php foreach ($schoolHandler->soon_expiring_schools as $value) { ?>
                                    <div class="sl-item <?php echo $value->remaining_days <= 30 ? "sl-danger" : "sl-primary" ?> p-b-md">
                                        <div class="sl-content">
                                            <div class="m-t-0 change_page a <?php echo $value->remaining_days <= 30 ? "text-danger animate-twice animated headShake" : "text-primary" ?>" page='school_profile' step='' args='&school_id=<?php echo $value->id; ?>'>
                                                <?php echo htmlspecialchars($value->name); ?>
                                            </div>
                                            <p class="text-muted"><?php echo $value->remaining_days == 0 ? TranslationHandler::get_static_text("TODAY") : $value->remaining_days == 1 ? TranslationHandler::get_static_text("TOMORROW") : $value->remaining_days . " " . strtolower(TranslationHandler::get_static_text("DAYS_REMAINING")); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover({trigger: "hover"});
    });</script>
