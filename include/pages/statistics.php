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
    switch($userHandler->_user->user_type_id)
    {
        case "1":
            $activity_limit = 3;
            $statisticsHandler->get_global_school_stats();
            $statisticsHandler->get_global_account_stats();
            $statisticsHandler->get_login_activity($activity_limit);

            echo date('j')-$activity_limit;

            echo '<pre>';
            var_dump($statisticsHandler->login_activity);
            echo '</pre>';
            ?>
            <div class="col-md-12 col-sm-12 p-v-0">
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class='panel-heading p-h-lg p-v-md'>
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("SCHOOL_FIND") ? ' a change_page" page="find_school" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_SCHOOL") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("SCHOOLS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
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
                                            foreach($statisticsHandler->school_type_amount as $key => $value)
                                            {   
                                                echo "{ label: '" . $key . "', data: " . $value . ", color: '" . $colors[$i] ."' }";
                                                if($i!=count($statisticsHandler->school_type_amount)-1)
                                                {
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
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("ACCOUNT_FIND") ? ' a change_page" page="find_school" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_ACCOUNT") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("ACCOUNTS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
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
                                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("DISTRIBUTION") . ":"; ?></label>
                                </div>
                                
                            </div>
                            
                            <div data-plugin="plot" data-options="
                                    [
                                        <?php
                                            $i = 0;
                                            foreach($statisticsHandler->account_type_amount as $key => $value)
                                            {   
                                                echo "{ label: '" . $key . "', data: " . $value . ", color: '" . $colors[$i] ."' }";
                                                if($i!=count($statisticsHandler->account_type_amount)-1)
                                                {
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
                            <h4 class="panel-title<?= (RightsHandler::has_user_right("ACCOUNT_FIND") ? ' a change_page" page="find_school" data-toggle="tooltip" data-placement="left" title="' . TranslationHandler::get_static_text("FIND_ACCOUNT") : '') ?>" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg" style="padding-right:30px;"></i><?php echo TranslationHandler::get_static_text("ACCOUNT_ACTIVITY"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
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
                                data:['<?= TranslationHandler::get_static_text("LECTURES") ?>']
                                },
                                calculable : true,
                                xAxis : [
                                    {
                                        type : 'category',
                                        boundaryGap : false,
                                        data : [
                                            <?php
                                            for($i = date('G')-(24*$activity_limit); $i < date('G')+1; $i++) {

                                                $hour = $i;

                                                while($hour < 0)
                                                {
                                                    $hour += 24;
                                                }

                                                echo $hour;

                                                if($i != date('G'))
                                                {
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
                                            for($i = date('j')-$activity_limit; $i < date('j')+1; $i++)
                                            {
                                                $day = $i > 0 ? $i : date("t", strtotime(date("Y-m-d -1 months"))) + $i;
                                                if(array_key_exists($day, $statisticsHandler->login_activity))
                                                {
                                                    for($j = date('G')-24; $j < date('G')+1; $j++)
                                                    {
                                                        $hour = $j;
                                                        while($hour < 0)
                                                        {
                                                            $hour += 24;
                                                        }

                                                        if(array_key_exists($hour, $statisticsHandler->login_activity[$day]))
                                                        {
                                                            echo $statisticsHandler->login_activity[$day][$hour];
                                                        }
                                                        else
                                                        {
                                                            echo 0;
                                                        }

                                                        if($j != date('G'))
                                                        {
                                                            echo ",";
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    echo "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
                                                }

                                                if($i != date('j'))
                                                {
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
    
            <?php        
            break;
        
        //LOKAL ADMIN DASHBOARD
        case "2":
            ?>

            <?php
            break;
    }
    ?>
    
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
    $('[data-toggle="popover"]').popover({trigger: "hover"});});</script>