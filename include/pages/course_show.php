<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$courseHandler = new CourseHandler();
$homeworkHandler = new HomeworkHandler();

$course_id = isset($_GET["course_id"]) ? $_GET["course_id"] : null;
$current_user = SessionKeyHandler::get_from_session("user", true);
$order_by = SettingsHandler::get_settings()->course_show_order;

if(!$courseHandler->get($course_id, "course") || !$courseHandler->get_multiple($course_id, "lecture") || !$courseHandler->get_multiple($course_id, "test")) {
    ErrorHandler::show_error_page();
    die();
}

$has_homework = $homeworkHandler->get_specific_course_homework($course_id);
?>


<div class="row">
    <div class="col-md-9">
        <div class="col-md-12 accordion" id="accordion">
            <div class="panel-heading" style="padding: 16px !important;">
                <h4 class="panel-title" style="float:left;"><?= TranslationHandler::get_static_text("LECTURES") ?></h4>
                <i class="zmdi zmdi-hc-lg zmdi-plus switch" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-lectures" aria-expanded="true" aria-controls="collapse-1" style="float:right;cursor:pointer;padding-left:5px;"></i>
                <i class="zmdi zmdi-hc-lg change_course_view <?= $order_by == 1 ? 'zmdi-format-list-bulleted' : 'zmdi-apps'; ?>" order="<?= $order_by ?>" page="course_show" args="&course_id=<?= $course_id; ?>" style="float:right;cursor:pointer;"></i><div style="clear:both;"></div>
            </div>
            <div style="border-top: 8px solid <?php echo htmlspecialchars($courseHandler->current_element->color); ?>;background: none !important; ">
                <div class="panel-body panel-collapse collapse in" id="collapse-lectures"  role="tabpanel" style="background: none !important; padding: 16px 0px !important; ">

                    <?php foreach($courseHandler->lectures as $value) { ?>
                    <?php if($order_by == 1) { ?>
                    <div class="col-sm-4 play_lecture" element_id="<?= $value->id; ?>">
                        
                        <div class="widget" style="cursor:pointer;" data-container="body" data-toggle="popover"  data-placement="bottom" data-trigger="hover" data-html="true" data-content="<?= $value->title . "" . (empty(trim($value->description, " \t\n\r\0\x0B\xC2\xA0")) ? '' : '<br/>') . "" . ($value->description != $value->title ? trim($value->description, " \t\n\r\0\x0B\xC2\xA0") : ''); ?>">
                            
                            <header class="widget-header" style="text-overflow: ellipsis; overflow: hidden;white-space:nowrap;padding: 0.7rem 1rem !important;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>">
                                <?php echo htmlspecialchars($value->title); ?>
                            </header>
                            <hr class="widget-separator m-0">
                            <div class="widget-body">
                                <?php echo $value->is_complete ? '<div class="ribbon"><span>'. TranslationHandler::get_static_text("COMPLETED") .'</span></div>' : (array_key_exists($value->id, $homeworkHandler->homework_all_lectures) && !$homeworkHandler->homework_all_lectures[$value->id]->is_complete ? '<div class="ribbon homework-ribbon"><span>'. TranslationHandler::get_static_text("HOMEWORK") .'</span></div>' : ''); ?>
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
                                                <td style="padding-left:15px;"><small class="text-color"><?php echo  $value->advanced < 1 ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD")  ?></small></td>
                                            </tr>
                                        </tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="col-md-12 play_lecture" element_id="<?= $value->id; ?>">
                        <div class="widget" style="cursor:pointer;margin-bottom:1rem !important;" data-container="body" data-toggle="popover"  data-placement="bottom" data-trigger="hover" data-html="true" data-content="<?= $value->title . "" . (empty(trim($value->description, " \t\n\r\0\x0B\xC2\xA0")) ? '' : '<br/>') . "" . ($value->description != $value->title ? trim($value->description, " \t\n\r\0\x0B\xC2\xA0") : ''); ?>">
                            <div class="widget-body" style="padding: 1.35rem !important">
                                <?php echo $value->is_complete ? '<div class="ribbon"><span>'. TranslationHandler::get_static_text("COMPLETED") .'</span></div>' : (array_key_exists($value->id, $homeworkHandler->homework_all_lectures) && !$homeworkHandler->homework_all_lectures[$value->id]->is_complete ? '<div class="ribbon homework-ribbon"><span>'. TranslationHandler::get_static_text("HOMEWORK") .'</span></div>' : ''); ?>
                                <div class="clearfix">
                                    <table style="width:100%;">
                                        <tr>
                                            <td style="width:80px;padding-top:4px !important;"><div class="pieprogress text-primary circle_progress" data-plugin="circleProgress" data-value="<?= $value->percent_progress/100 ?>" data-thickness="6" data-size="20" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .2)" data-fill="{&quot;color&quot;: &quot;<?= get_progress_color($value->percent_progress); ?>&quot;}" style="float:left;"></div><div data-plugin="counterUp" style="float:left;padding-left:10px;"><?= $value->percent_progress ?></div><div style="float:left;">%</div></td>
                                            <td></td>
                                            <td style="<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>"><?= htmlspecialchars($value->title); ?></td>
                                            <td style="text-align:right;padding-left:20px;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>"><small class="text-color"><?= TranslationHandler::get_static_text("POINT_AMOUNT"); ?>:</small><small class="text-color" style="padding-left:10px;"><?= $value->points; ?></small></td>
                                            <td style="text-align:right;width:20%;padding-left:20px;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>"><small class="text-color"><?= TranslationHandler::get_static_text("DIFFICULTY"); ?>:</small><small class="text-color" style="padding-left:10px;"><?php echo  $value->advanced < 1 ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD")  ?></small></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>

                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="panel-heading accordion" id="accordion-2">
                <h4 class="panel-title" style="float:left;"><?= TranslationHandler::get_static_text("TESTS") ?></h4>
                <i class="zmdi zmdi-hc-lg zmdi-plus switch" role="button" data-toggle="collapse" data-parent="#accordion-2" href="#collapse-tests" aria-expanded="true" style="float:right;cursor:pointer;padding-left:5px;"></i>
                <i class="zmdi zmdi-hc-lg change_course_view <?= $order_by == 1 ? 'zmdi-format-list-bulleted' : 'zmdi-apps'; ?>" order="<?= $order_by ?>" page="course_show" args="&course_id=<?= $course_id; ?>" style="float:right;cursor:pointer;"></i><div style="clear:both;"></div>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body panel-collapse collapse in" id="collapse-tests"  role="tabpanel" style="background: none !important; padding: 16px 0px !important; ">

                    <?php foreach($courseHandler->tests as $value) { ?>
                    <?php if($order_by == 1) { ?>
                    <div class="col-sm-4 play_test" element_id="<?= $value->id; ?>">
                        <div class="widget" style="cursor:pointer;" data-container="body" data-toggle="popover"  data-placement="bottom" data-trigger="hover" data-html="true" data-content="<?= $value->title . "" . (empty(trim($value->description, " \t\n\r\0\x0B\xC2\xA0")) ? '' : '<br/>') . "" . ($value->description != $value->title ? trim($value->description, " \t\n\r\0\x0B\xC2\xA0") : ''); ?>">
                            <header class="widget-header" style="text-overflow: ellipsis; overflow: hidden;white-space:nowrap;padding: 0.7rem 1rem !important;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>">
                                <?php echo $value->title; ?>
                            </header>
                            <hr class="widget-separator m-0">
                            <div class="widget-body">
                                <?php echo $value->is_complete ? '<div class="ribbon"><span>'. TranslationHandler::get_static_text("COMPLETED") .'</span></div>' : (array_key_exists($value->id, $homeworkHandler->homework_all_tests) && !$homeworkHandler->homework_all_tests[$value->id]->is_complete ? '<div class="ribbon homework-ribbon"><span>'. TranslationHandler::get_static_text("HOMEWORK") .'</span></div>' : ''); ?>
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
                                                <td style="padding-left:15px;"><small class="text-color"><?php echo  $value->advanced < 1 ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD")  ?></small></td>
                                            </tr>
                                        </tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="col-md-12 play_test" element_id="<?= $value->id; ?>">
                        <div class="widget" style="cursor:pointer;margin-bottom:1rem !important;" data-container="body" data-toggle="popover"  data-placement="bottom" data-trigger="hover" data-html="true" data-content="<?= $value->title . "" . (empty(trim($value->description, " \t\n\r\0\x0B\xC2\xA0")) ? '' : '<br/>') . "" . ($value->description != $value->title ? trim($value->description, " \t\n\r\0\x0B\xC2\xA0") : ''); ?>">
                            <div class="widget-body" style="padding: 1.35rem !important">
                                <?php echo $value->is_complete ? '<div class="ribbon"><span>'. TranslationHandler::get_static_text("COMPLETED") .'</span></div>' : (array_key_exists($value->id, $homeworkHandler->homework_all_tests) && !$homeworkHandler->homework_all_tests[$value->id]->is_complete ? '<div class="ribbon homework-ribbon"><span>'. TranslationHandler::get_static_text("HOMEWORK") .'</span></div>' : ''); ?>
                                  <div class="clearfix">
                                    <table style="width:100%;">
                                        <tr>
                                            <td style="width:80px;padding-top:4px !important;"><div class="pieprogress text-primary circle_progress" data-plugin="circleProgress" data-value="<?= $value->percent_progress/100 ?>" data-thickness="6" data-size="20" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .2)" data-fill="{&quot;color&quot;: &quot;<?= get_progress_color($value->percent_progress); ?>&quot;}" style="float:left;"></div><div data-plugin="counterUp" style="float:left;padding-left:10px;"><?= $value->percent_progress ?></div><div style="float:left;">%</div></td>
                                            <td></td>
                                            <td style="<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>"><?= $value->title; ?></td>
                                            <td style="text-align:right;padding-left:20px;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>"><small class="text-color"><?= TranslationHandler::get_static_text("POINT_AMOUNT"); ?>:</small><small class="text-color" style="padding-left:10px;"><?= $value->points; ?></small></td>
                                            <td style="text-align:right;width:20%;padding-left:20px;<?php echo $value->is_complete ? 'opacity:0.5;' : ''; ?>"><small class="text-color"><?= TranslationHandler::get_static_text("DIFFICULTY"); ?>:</small><small class="text-color" style="padding-left:10px;"><?php echo  $value->advanced < 1 ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD")  ?></small></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
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
                                    <i class="zmdi zmdi-hc-lg <?php echo isset($value["lecture_type"]) ? 'zmdi-movie' : 'zmdi-graduation-cap" style="margin-left:-2px;'; ?>"></i>
                                </div>
                                <div style="display: table-cell;width: 100%;">
                                    <div style="width:100%;bottom:0;text-align:right;">
                                        <?= htmlspecialchars($value["title"]) ?><br>
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
                        echo "<p>".TranslationHandler::get_static_text("NO_COMPLETED_COURSES_OR_TESTS")."</p>";
                    }
                ?>
                </div>
            </div>
        </div>
        
        <?php if($current_user->user_type_id != 1) { ?>
        <div class="col-md-12">
            <div class="panel-heading">
                <h4 class="panel-title"><?= TranslationHandler::get_static_text("LATEST_HOMEWORK") ?></h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; ">
                <?php
                    if($has_homework) {
                        echo '<div class="widget"><div class="widget-body" style="padding: 0px !important;">';
                        
                        $i = 0;
                        usort($homeworkHandler->homework, function($a, $b) {
                            return strtotime($a->date_expire) - strtotime($b->date_expire);
                        });
                        foreach($homeworkHandler->homework as $value) {
                            if($i > 4) {
                                break;
                            }
                            if(strtotime($value->date_expire) < strtotime(date("Y-m-d"))) {
                                continue;
                            }
                            $i++;
                            ?>
                            <div class="change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" style="cursor:pointer;padding: 10px 16px;">
                                <div style="display: table-cell;vertical-align: middle;">
                                    <i class="zmdi zmdi-hc-lg zmdi-assignment-o"></i>
                                </div>
                                <div style="display: table-cell;width: 100%;">
                                    <div style="width:100%;bottom:0;text-align:right;">
                                        <?= htmlspecialchars($value->title); ?><br>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <hr class="widget-separator m-0">
                            <?php
                        }
                        echo '</div></div>';
                    } else {
                        echo "<p>".TranslationHandler::get_static_text("NO_COMPLETED_COURSES_OR_TESTS")."</p>";
                    }
                ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
    
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({trigger: "hover"});
    });
    
    $(document).on("click", ".switch", function() {
        if($(this).hasClass("zmdi-plus")) {
            $(this).toggleClass("zmdi-plus zmdi-minus");
        } else {
            $(this).toggleClass("zmdi-minus zmdi-plus");
        }
    });
</script>