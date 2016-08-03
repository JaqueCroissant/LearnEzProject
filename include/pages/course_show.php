<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();

$course_id = isset($_GET["course_id"]) ? $_GET["course_id"] : null;

if(!$courseHandler->get($course_id, "course") || !$courseHandler->get_multiple($course_id, "lecture") || !$courseHandler->get_multiple($course_id, "test")) {
    ErrorHandler::show_error_page();
    die();
}
?>


<div class="row">
    <div class="col-md-9">
        <div class="col-md-12">
            <div class="panel-heading">
                <h4 class="panel-title"><?= TranslationHandler::get_static_text("LECTURES") ?></h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; padding: 16px 0px !important; ">

                    <?php foreach($courseHandler->lectures as $value) { ?>
                    <div class="col-md-4">
                        
                        <div class="widget" style="cursor:pointer;" data-container="body" data-toggle="popover" data-delay='{"show":"300", "hide":"300"}' data-placement="right" data-trigger="hover" data-content="<?= $value->description; ?>">
                            
                            <header class="widget-header" style="padding: 0.7rem 1rem !important;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>">
                                <?php echo $value->title; ?>
                            </header>
                            <hr class="widget-separator m-0">
                            <div class="widget-body">
                                <?php echo $value->is_complete ? '<div class="ribbon"><span>'. TranslationHandler::get_static_text("COMPLETED") .'</span></div>' : ''; ?>
                                <div class="clearfix">
                                    <div class="pull-left">
                                        <div class="pieprogress text-primary circle_progress" data-plugin="circleProgress" data-value="<?= $value->percent_progress/100 ?>" data-thickness="6" data-size="70" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .2)" data-fill="{&quot;color&quot;: &quot;<?= get_progress_color($value->percent_progress); ?>&quot;}">
                                            <strong style="color: #6a6c6f !important;margin-top: -14px;font-size: 16px" ><div data-plugin="counterUp" style="display:inline-block"><?= $value->percent_progress ?></div><div style="display:inline-block">%</div></strong>
                                        </div>
                                    </div>
                                    <div class="pull-right" style="text-align:right;">
                                        <div>
                                        <table style="width: auto; float:right;text-align:right;margin-bottom:5px;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>">
                                            <tbody><tr>
                                                    <td><small class="text-color"><?= TranslationHandler::get_static_text("POINT_AMOUNT"); ?>:</small></td>
                                                <td style="padding-left:15px;text-align:right;"><small class="text-color"><?= $value->points; ?></small></td>
                                            </tr>
                                            <tr>
                                                <td><small class="text-color"><?= TranslationHandler::get_static_text("LENGTH"); ?>:</small></td>
                                                <td style="padding-left:15px;"><small class="text-color"><?= gmdate("i:s", $value->time_length); ?></small></td>
                                            </tr>
                                            <tr>
                                                <td><small class="text-color"><?= TranslationHandler::get_static_text("DIFFICULTY"); ?>:</small></td>
                                                <td style="padding-left:15px;"><small class="text-color"><?php echo  $value->advanced ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD")  ?></small></td>
                                            </tr>
                                        </tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="panel-heading">
                <h4 class="panel-title"><?= TranslationHandler::get_static_text("TESTS") ?></h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; padding: 16px 0px !important; ">

                    <?php foreach($courseHandler->tests as $value) { ?>
                    <div class="col-md-4">
                        <div class="widget" style="cursor:pointer;" data-container="body" data-toggle="popover" data-delay='{"show":"300", "hide":"300"}' data-placement="right" data-trigger="hover" data-content="<?= $value->description; ?>">
                            <header class="widget-header" style="padding: 0.7rem 1rem !important;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>">
                                <?php echo $value->title; ?>
                            </header>
                            <hr class="widget-separator m-0">
                            <div class="widget-body">
                                <?php echo $value->is_complete ? '<div class="ribbon"><span>'. TranslationHandler::get_static_text("COMPLETED") .'</span></div>' : ''; ?>
                                <div class="clearfix">
                                    <div class="pull-left">
                                        <div class="pieprogress text-primary circle_progress" data-plugin="circleProgress" data-value="<?= $value->percent_progress/100 ?>" data-thickness="6" data-size="70" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .2)" data-fill="{&quot;color&quot;: &quot;<?= get_progress_color($value->percent_progress); ?>&quot;}">
                                            <strong style="color: #6a6c6f !important;margin-top: -14px;font-size: 16px" ><div data-plugin="counterUp" style="display:inline-block"><?= $value->percent_progress ?></div><div style="display:inline-block">%</div></strong>
                                        </div>
                                    </div>
                                    <div class="pull-right" style="text-align:right;">
                                        <div>
                                        <table style="width: auto; float:right;text-align:right;margin-bottom:5px;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>">
                                            <tbody><tr>
                                                <td><small class="text-color"><?= TranslationHandler::get_static_text("POINT_AMOUNT"); ?>:</small></td>
                                                <td style="padding-left:15px;text-align:right;"><small class="text-color"><?= $value->points; ?></small></td>
                                            </tr>
                                            <tr>
                                                <td><small class="text-color"><?= TranslationHandler::get_static_text("TOTAL_STEPS"); ?>:</small></td>
                                                <td style="padding-left:15px;"><small class="text-color"><?= $value->total_steps ?></small></td>
                                            </tr>
                                            <tr>
                                                <td><small class="text-color"><?= TranslationHandler::get_static_text("DIFFICULTY"); ?>:</small></td>
                                                <td style="padding-left:15px;"><small class="text-color"><?php echo  $value->advanced ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD")  ?></small></td>
                                            </tr>
                                        </tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="col-md-12">
            <div class="panel-heading">
                <h4 class="panel-title"><?= TranslationHandler::get_static_text("LAST_COMPLETED") ?></h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; ">
                <?php
                    $courseHandler->get_last_completed($course_id);
                    if(!empty($courseHandler->last_elements)) {
                        echo '<div class="widget"><div class="widget-body" style="padding: 0px !important;">';
                        
                        foreach($courseHandler->last_elements as $value) {
                            $date_to_string = time_elapsed($value["complete_date"]);
                            ?>
                            <div style="padding: 10px 16px;">
                                <div style="display: table-cell;vertical-align: middle;">
                                    <i class="zmdi zmdi-hc-lg <?php echo $value["lecture_type"] ? 'zmdi-movie' : 'zmdi-graduation-cap" style="margin-left:-2px;'; ?>"></i>
                                </div>
                                <div style="display: table-cell;width: 100%;">
                                    <div style="width:100%;bottom:0;text-align:right;">
                                        <?= $value["title"] ?><br>
                                        <small class="text-muted"><?=$date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) .' ' . TranslationHandler::get_static_text("DATE_AGO"); ?></small>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <hr class="widget-separator m-0">
                            <?php
                        }
                        echo '</div></div>';
                    } else {
                        echo "<p>Du har endnu ikke klaret nogen kurser eller tests.</p>";
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover();
    });
</script>