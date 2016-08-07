<?php
require_once 'require.php';
require_once '../../include/handler/calendarHandler.php';

$selected_date = isset($_GET["selected_date"]) ? $_GET["selected_date"] : 0;
$calendarHandler = new CalendarHandler($selected_date);
?>

<div class="profile-header" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem !important;background: #fff;padding: 20px 0px;">
    <div class="row" style="margin:0px !important;">
        <div class="col-md-9 col-center">
            <div class="fc-toolbar">
                <div style="float:left;margin-left:3px;">
                    <a href="javascript:void(0)" page="homework_overview"  args="&selected_date=<?= $selected_date-1; ?>" id="homeword_overview" class="change_page btn btn-default"><i class="fa fa-chevron-left"></i></a>
                </div>
                
                <div style="float:left;margin: 0px 5px;">
                    <a href="javascript:void(0)" page="homework_overview"  args="&selected_date=0" id="homeword_overview" class="change_page btn btn-default"><?= TranslationHandler::get_static_text("GO_TO_TODAY") ?></a>
                </div>
                
                <div style="float:left;">
                    <a href="javascript:void(0)" page="homework_overview"  args="&selected_date=<?= $selected_date+1; ?>" id="homeword_overview" class="change_page btn btn-default"><i class="fa fa-chevron-right"></i></a>
                </div>

                <div style="float:right;margin-right:16px;margin-top:10px;">
                    <?= $calendarHandler->generate_current_date_string() ?>
                </div>

                <div class="fc-center" style="margin-top:10px;"><h2><?= $calendarHandler->current_date->month_title . " " . $calendarHandler->current_date->year; ?></h2></div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
    
    <div class="row" style="margin:0px !important;">
        <div class="col-md-9 col-center">
            <?php foreach(array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN") as $value) { ?>
            <div class="calendar-element">
                <div class="calendar-top-element-container"><?= TranslationHandler::get_static_text("WEEK_DAY_".$value) ?></div>
            </div>
            <?php } ?>
            <div style="clear:both;"></div>
        </div>
    </div>
    <?php 
    foreach($calendarHandler->current_dates as $key => $value) {
        if($key == 0 || $key % 7 == 0) {
            echo '<div class="row" style="margin:0px !important;"><div class="col-md-9 col-center">';
        }
        
        echo '<div class="calendar-element" data-toggle="tooltip" title="'. $value->day_title .' - '. $value->day .' '. $value->month_title .'">
                <div class="calendar-element-container '. ($value->is_today ? 'calendar-element-container-today' : '') .' '. (!$value->in_current_month ? 'calendar-element-disabled' : '') .'">
                    <div class="calendar-element-date">'. $value->day .'</div>
                    <div style="clear:both;"></div>
                    <div class="calendar-element-content">
                        
                    </div>
                </div>
            </div>';
        
        if(($key+1) % 7 == 0 ) {
            echo '<div style="clear:both;"></div></div></div>';
        }
    }
    ?>
</div>



<div class="row">
    <div class="col-md-9">
        <div class="col-md-12" style="padding-right:0.25rem;padding-left: 0.25rem;">
            <div class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md" >
                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-assignment-o zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("LATEST_HOMEWORK") ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body user-description">
                    <div class="center description" >sdfsfasd</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12" style="padding-right:0.25rem;padding-left: 0.25rem;">
            <div class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md" >
                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-alert-circle-o zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("INCOMPLETE_HOMEWORK") ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body user-description">
                    <div class="center description" >sdfsfasd</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md">
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-info-outline zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("INFORMATION") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>