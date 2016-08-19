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

$colors = ['rgb(103, 157, 198)', 'rgb(57, 128, 181)', '#ffa000', '#e64a19', '#4caf50', '#303f9f'];
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">

    <?php
    switch ($userHandler->_user->user_type_id) {
        case "1":
            $activity_limit = 3;
            $statisticsHandler->get_global_school_stats();
            $statisticsHandler->get_global_account_stats();
            $statisticsHandler->get_course_stats();
            $statisticsHandler->get_login_activity($activity_limit);

            $all_logins = 0;

            foreach($statisticsHandler->login_activity["all"] as $value)
            {
                if(max($value)>$all_logins)
                {
                    $all_logins = max($value);
                }
            }

            ?>
            <div class="col-md-12">
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("SCHOOL_FIND") ? ' a change_page" page="find_school" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_SCHOOL") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("SCHOOLS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body school">
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AMOUNT") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->school_count; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("OPEN_P") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->schools_open; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("CLASSES") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->school_classes_global; ?></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPES") . ":"; ?></label>
                                </div>
                            </div>
                            <div data-plugin="plot" data-options="
                                 [
                                 <?php
                                 $i = 0;
                                 foreach ($statisticsHandler->school_type_amount as $key => $value) {
                                     echo "{ label: '" . $key . "', data: " . $value . ", color: '" . $colors[$i] . "' }";
                                     if ($i != count($statisticsHandler->school_type_amount) - 1) {
                                         echo ",";
                                     }
                                     $i++;
                                 }
                                 ?>
                                 ],
                                 {
                                 series: {
                                 pie: { show: true }
                                 },
                                 legend: { show: false },
                                 grid: { hoverable: true },
                                 tooltip: {
                                 show: true,
                                 content: '%s %p.0%',
                                 defaultTheme: true
                                 }
                                 }" style="height: 300px; width: 100%; padding: 0px; position: relative;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("ACCOUNT_FIND") ? ' a change_page" page="find_account" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_ACCOUNT") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("ACCOUNTS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body account">
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AMOUNT") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->account_count; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("OPEN_P") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->accounts_open; ?></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USER_TYPE") . ":"; ?></label>
                                </div>

                            </div>

                            <div data-plugin="plot" data-options="
                                 [
                                    <?php
                                    $i = 0;
                                    foreach ($statisticsHandler->account_type_amount as $key => $value) {
                                        echo "{ label: '" . $key . "', data: " . $value . ", color: '" . $colors[$i] . "' }";
                                        if ($i != count($statisticsHandler->account_type_amount) - 1) {
                                            echo ",";
                                        }
                                        $i++;
                                    }
                                    ?>
                                 ],
                                 {
                                 series: {
                                 pie: { show: true }
                                 },
                                 legend: { show: false },
                                 grid: { hoverable: true },
                                 tooltip: {
                                 show: true,
                                 content: '%s %p.0%',
                                 defaultTheme: true
                                 }
                                 }" style="height: 300px; width: 100%; padding: 0px; position: relative;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("COURSE_FIND") ? ' a change_page" page="find_course" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_COURSE") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-graduation-cap zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("COURSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body course">
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AMOUNT") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->global_course_amount; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("LECTURES") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->global_lectures_amount; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("TESTS") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->global_test_amount; ?></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("DISTRIBUTION") . ":"; ?></label>
                                </div>

                            </div>

                            <div data-plugin="plot" data-options="
                                 [
                                    <?php
                                    $i = 0;
                                    foreach ($statisticsHandler->course_os_distribution as $key => $value) {
                                        echo "{ label: '" . $key . "', data: " . $value . ", color: '" . $colors[$i] . "' }";
                                        if ($i != count($statisticsHandler->course_os_distribution) - 1) {
                                            echo ",";
                                        }
                                        $i++;
                                    }
                                    ?>
                                 ],
                                 {
                                 series: {
                                 pie: { show: true }
                                 },
                                 legend: { show: false },
                                 grid: { hoverable: true },
                                 tooltip: {
                                 show: true,
                                 content: '%s %p.0%',
                                 defaultTheme: true
                                 }
                                 }" style="height: 300px; width: 100%; padding: 0px; position: relative;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>
    <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading p-h-lg p-v-md" >
                            <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("LOGIN_ACTIVITY") ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body user-progress">


                            <div data-plugin="chart" data-options="{
                                 tooltip : {
                                 trigger: 'axis'
                                 },
                                 legend: {
                                 selected:
                                 {
                                    <?php
                                    echo "'" . TranslationHandler::get_static_text("ALL") . "' : false";
                                    ?>
                                                             },
                                                             data:[<?php
                                    foreach ($statisticsHandler->login_activity as $key => $value) {
                                        $text = $key == "all" ? TranslationHandler::get_static_text("ALL") : $key;
                                        echo "'" . $text . "',";
                                    }
                                    ?>]
                                 },
                                 calculable : true,
                                 xAxis : [
                                 {
                                 type : 'category',
                                 boundaryGap : false,
                                 axisLabel : {
                                    rotate: -60,
                                   margin: 10
                                 },
                                 data : [
                                 <?php
                                 for ($i = 0; $i < 24 * $activity_limit; $i++) {

                                     $hour = $i % 24;
                                     echo "'" . ($hour < 10 ? "0" : "") . $hour . ":00'";
                                     

                                     if ($i != 24 * $activity_limit-1) {
                                         echo ",";
                                     }
                                 }
                                 ?>
                                 ]
                                 }
                                 ],
                                 yAxis : [
                                 {
                                    type : 'value',
                                    max : <?= $all_logins * 1.1 ?>
                                 }
                                 ],
                                 series : [<?php
                                 foreach ($statisticsHandler->login_activity as $key => $value) {
                                     ?>
                                     {
                                     name:'<?= $key == "all" ? TranslationHandler::get_static_text("ALL") : $key ?>',
                                     type:'line',
                                     symbolSize:0,
                                     smooth:true,
                                     itemStyle: {normal: {areaStyle: {type: 'default'}}},
                                     data:
                                     [
                                        <?php
                                            $iterations = 0;
                                            $limit = ($activity_limit - 1);
                                            $date = date('Y-m-d', strtotime(date("Y-m-d") . "-" . $limit . " days"));
                                            while ($iterations < 24 * $activity_limit) {
                                                if ($iterations == 24 || $iterations == 48) {
                                                    $limit -= 1;
                                                    $date = date('Y-m-d', strtotime(date("Y-m-d") . "-" . $limit . " days"));
                                                }

                                                $hour = $iterations % 24;

                                                if (array_key_exists($date, $value) && array_key_exists($hour, $value[$date])) {
                                                    echo $value[$date][$hour];
                                                } else {
                                                    echo 0;
                                                }


                                                if ($iterations != 24 * $activity_limit) {
                                                    echo ", ";
                                                }

                                                $iterations++;
                                            }
                                        ?>
                                     ]
                                     },
                                     <?php
                                 }
                                 ?>

                                 ]
                                 }" style="height: 300px;"></div>
                        </div>
                    </div>


                </div>
            </div>
            <?php
            break;

            //LOKAL ADMIN DASHBOARD
            case "2":
                                 
            $activity_limit = 3;
            $statisticsHandler->get_global_account_stats();
            $statisticsHandler->get_course_stats();
            $statisticsHandler->get_total_students();
            $statisticsHandler->get_login_activity($activity_limit);
            $all_logins = 0;

            foreach($statisticsHandler->login_activity["all"] as $value)
            {
                if(max($value)>$all_logins)
                {
                    $all_logins = max($value);
                }
            }

            ?>
            <div class="col-md-12 col-sm-12 p-v-0">
                

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("ACCOUNT_FIND") ? ' a change_page" page="find_account" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_ACCOUNT") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("ACCOUNTS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body account">
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AMOUNT") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->account_count; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("OPEN_P") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->accounts_open; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("TEACHERS") . " " . strtolower(TranslationHandler::get_static_text("AND")) . " " . strtolower(TranslationHandler::get_static_text("STUDENTS")) . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->account_student_teacher_count; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("MAXIMUM") . " " . strtolower(TranslationHandler::get_static_text("TEACHERS")) . " " . strtolower(TranslationHandler::get_static_text("AND")) . " " . strtolower(TranslationHandler::get_static_text("STUDENTS")) . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->account_max; ?></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USER_TYPE") . ":"; ?></label>
                                </div>

                            </div>

                            <div data-plugin="plot" data-options="
                                 [
                                    <?php
                                    $i = 0;
                                    foreach ($statisticsHandler->account_type_amount as $key => $value) {
                                        echo "{ label: '" . $key . "', data: " . $value . ", color: '" . $colors[$i] . "' }";
                                        if ($i != count($statisticsHandler->account_type_amount) - 1) {
                                            echo ",";
                                        }
                                        $i++;
                                    }
                                    ?>
                                 ],
                                 {
                                 series: {
                                 pie: { show: true }
                                 },
                                 legend: { show: false },
                                 grid: { hoverable: true },
                                 tooltip: {
                                 show: true,
                                 content: '%s %p.0%',
                                 defaultTheme: true
                                 }
                                 }" style="height: 300px; width: 100%; padding: 0px; position: relative;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_page_right("COURSE_OVERVIEW") ? ' a change_page" page="course_overview" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("COURSE_OVERVIEW") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-graduation-cap zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("COURSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body course">
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AMOUNT") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->global_course_amount; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("LECTURES") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->global_lectures_amount; ?></span>
                            </div>
                            <div>
                                <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("TESTS") . ":"; ?></label>
                                <span class="pull-right"><?php echo $statisticsHandler->global_test_amount; ?></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("ACTIVE_STUDENTS_PER_COURSE") . ":"; ?></label>
                                </div>

                            </div>

                            <div data-plugin="chart" data-options="{
                                
                                
                                 tooltip : {
                                  trigger: 'item'
                                },
                                legend: {
                                
                                  data:['<?php echo TranslationHandler::get_static_text("LECTURES") ?>','<?php echo TranslationHandler::get_static_text("TESTS") ?>']
                                },
                                calculable : true,
                                xAxis : [
                                  {
                                    type : 'category',
                                    data : 
                                    [<?php
                                        $i = 0;
                                        
                                        foreach($statisticsHandler->course_titles as $key => $value)
                                        {
                                            echo "'" . $key . "'";
                                            
                                            if($i != count($statisticsHandler->course_titles))
                                            {
                                                echo ", ";
                                            }
                                            $i++;
                                        }
                                    ?>]
                                  }
                                ],
                                yAxis : [
                                  {
                                    type : 'value',
                                    max : <?= $statisticsHandler->student_total * 1.2 ?>
                                  }
                                ],
                                series : [
                                  {
                                    name:'<?php echo TranslationHandler::get_static_text("TOTAL") ?>',
                                    type:'line',
                                    symbolSize:0,
                                    tooltip: { show: 0 },
                                    data:[<?php
                                            $i = 0;
                                            foreach($statisticsHandler->course_titles as $value)
                                            {
                                                echo "";
                                                
                                                if($i != count($statisticsHandler->course_titles))
                                                {
                                                    echo ", ";
                                                }
                                                $i++;
                                            }
                                    
                                         ?>],
                                    markLine : {
                                      lineStyle: {
                                            normal: {
                                                type: 'dashed'
                                            }
                                        },
                                        data : [
                                            [{name : 'min', value: <?= $statisticsHandler->student_total ?>, xAxis: -1, yAxis: <?= $statisticsHandler->student_total ?>}, {name : 'max', xAxis: <?= $i ?>, yAxis: <?= $statisticsHandler->student_total ?>}]
                                        ]
                                    }
                                  },
                                  {
                                    name:'<?php echo TranslationHandler::get_static_text("LECTURES") ?>',
                                    type:'bar',
                                    data:[<?php
                                            $i = 0;
                                            foreach($statisticsHandler->course_titles as $value)
                                            {
                                                echo count($value["lectures"]);
                                                
                                                if($i != count($statisticsHandler->course_titles))
                                                {
                                                    echo ", ";
                                                }
                                            }
                                    
                                         ?>],
                                    
                                  },
                                  {
                                    name:'<?php echo TranslationHandler::get_static_text("TESTS") ?>',
                                    type:'bar',
                                    data:[<?php
                                            $i = 0;
                                            foreach($statisticsHandler->course_titles as $value)
                                            {
                                                echo count($value["tests"]);
                                                
                                                if($i != count($statisticsHandler->course_titles))
                                                {
                                                    echo ", ";
                                                }
                                                $i++;
                                            }
                                    
                                         ?>]
                                  }
                                ]
                                }" style="height: 300px; -webkit-tap-highlight-color: transparent; -webkit-user-select: none; cursor: default; background-color: rgba(0, 0, 0, 0);" _echarts_instance_="1471422121737"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading p-h-lg p-v-md" >
                            <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("LOGIN_ACTIVITY") ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="panel-body user-progress">


                            <div data-plugin="chart" data-options="{
                                 tooltip : {
                                 trigger: 'axis'
                                 },
                                 legend: {
                                 selected:
                                 {
                                    <?php
                                    echo "'" . TranslationHandler::get_static_text("ALL") . "' : false";
                                    ?>
                                                             },
                                                             data:[<?php
                                    foreach ($statisticsHandler->login_activity as $key => $value) {
                                        $text = $key == "all" ? TranslationHandler::get_static_text("ALL") : $key;
                                        echo "'" . $text . "',";
                                    }
                                    ?>]
                                 },
                                 calculable : true,
                                 xAxis : [
                                 {
                                 type : 'category',
                                 boundaryGap : false,
                                 axisLabel : {
                                    rotate: -60,
                                   margin: 10
                                 },
                                 data : [
                                 <?php
                                 for ($i = 0; $i < 24 * $activity_limit; $i++) {

                                     $hour = $i % 24;
                                     echo "'" . ($hour < 10 ? "0" : "") . $hour . ":00'";
                                     

                                     if ($i != 24 * $activity_limit-1) {
                                         echo ",";
                                     }
                                 }
                                 ?>
                                 ]
                                 }
                                 ],
                                 yAxis : [
                                 {
                                 type : 'value',
                                 max : <?= $all_logins * 1.1 ?>
                                 }
                                 ],
                                 series : [<?php
                                 foreach ($statisticsHandler->login_activity as $key => $value) {
                                    if($key == "all")
                                    {
                                        ?>
                                        
                                        {
                                            name:'<?= TranslationHandler::get_static_text("ALL")?>',
                                            type:'line',
                                            symbolSize:0,
                                            smooth:true,
                                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                                            data:
                                            [
                                               <?php
                                                   $iterations = 0;
                                                   $limit = ($activity_limit - 1);
                                                   $date = date('Y-m-d', strtotime(date("Y-m-d") . "-" . $limit . " days"));
                                                   while ($iterations < 24 * $activity_limit) {
                                                       if ($iterations == 24 || $iterations == 48) {
                                                           $limit -= 1;
                                                           $date = date('Y-m-d', strtotime(date("Y-m-d") . "-" . $limit . " days"));
                                                       }

                                                       $hour = $iterations % 24;

                                                       if (array_key_exists($date, $value) && array_key_exists($hour, $value[$date])) {
                                                           echo $value[$date][$hour];
                                                       } else {
                                                           echo 0;
                                                       }

                                                       if ($iterations != 24 * $activity_limit-1) {
                                                           echo ", ";
                                                       }

                                                       $iterations++;
                                                   }
                                               ?>
                                            ],
                                        },
                                 
                                        <?php
                                    }
                                    else
                                    {
                                    
                                     ?>
                                     {
                                     name:'<?= $key == "all" ? TranslationHandler::get_static_text("ALL") : $key ?>',
                                     type:'line',
                                     symbolSize:0,
                                     smooth:true,
                                     itemStyle: {normal: {areaStyle: {type: 'default'}}},
                                     data:
                                     [
                                        <?php
                                            $iterations = 0;
                                            $limit = ($activity_limit - 1);
                                            $date = date('Y-m-d', strtotime(date("Y-m-d") . "-" . $limit . " days"));
                                            while ($iterations < 24 * $activity_limit) {
                                                if ($iterations == 24 || $iterations == 48) {
                                                    $limit -= 1;
                                                    $date = date('Y-m-d', strtotime(date("Y-m-d") . "-" . $limit . " days"));
                                                }

                                                $hour = $iterations % 24;

                                                if (array_key_exists($date, $value) && array_key_exists($hour, $value[$date])) {
                                                    echo $value[$date][$hour];
                                                } else {
                                                    echo 0;
                                                }

                                                if ($iterations != 24 * $activity_limit-1) {
                                                    echo ", ";
                                                }

                                                $iterations++;
                                            }
                                        ?>
                                     ]
                                     },
                                     <?php
                                    }
                                 }
                                 ?>

                                 ]
                                 }" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                                 <?php
                                 break;
                                 
        default:
            ErrorHandler::show_error_page("INSUFFICIENT_RIGHTS");
            die();
                         }
                         ?>

    </div>
    <script src="assets/js/include_app.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover({trigger: "hover"});
            var max_height = Math.max($(".school").height(), $(".account").height(), $(".course").height());
            $(".school").height(max_height); $(".account").height(max_height); $(".course").height(max_height);
        });</script>