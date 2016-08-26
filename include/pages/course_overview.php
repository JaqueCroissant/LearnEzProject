<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();
$courseHandler->get_courses();
?>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <?php
            foreach($courseHandler->courses as $value) {
            ?>
            <div class="col-md-6 change_page" style="cursor:pointer;" id="course_show" page="course_show" args="&course_id=<?php echo $value->id; ?>">
                <div class="widget stats-widget" data-container="body" <?= !empty(trim($value->description, " \t\n\r\0\x0B\xC2\xA0")) ? 'data-toggle="popover"' : ''; ?>  data-placement="bottom" data-trigger="hover" data-content="<?= htmlspecialchars($value->description); ?>">
                    <div class="widget-body clearfix">
                        <div style="display: table-cell;width: 100%;"><img style="width:130px;height:130px;" src="assets/images/thumbnails/<?php echo htmlspecialchars($value->image_filename); ?>" alt=""></div>
                        <div style="display: table-cell;vertical-align: middle;white-space: nowrap;">
                            <table>
                                <tr>
                                    <td>
                                        <h3 class="widget-title text-primary" style="color: <?php echo $value->color; ?> !important;font-size:18px !important;text-align:right;"><?php echo htmlspecialchars($value->title); ?></h3>
                                        <table style="width: auto; float:right;">
                                            <tr>
                                                <td><small class="text-color"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_LECTURES"); ?>:</small></td>
                                                <td style="padding-left:10px;text-align:right;"><small class="text-color"><?php echo $value->amount_of_lectures; ?></small></td>
                                            </tr>
                                            <tr>
                                                <td><small class="text-color"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_TESTS"); ?>:</small></td>
                                                <td style="padding-left:10px;text-align:right;"><small class="text-color"><?php echo $value->amount_of_tests; ?></small></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 0px 30px;">
                                        <div class="pieprogress text-primary circle_progress" style="width:80px !important;margin-top:5px;" data-plugin="circleProgress" data-value="<?= $value->overall_progress/100 ?>" data-thickness="9" data-size="100" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .2)" data-fill="{&quot;color&quot;: &quot;<?= get_progress_color($value->overall_progress); ?>&quot;}">
                                            <strong style="color: #6a6c6f !important;margin-left:13px;font-size: 16px" ><div data-plugin="counterUp" style="display:inline-block"><?= $value->overall_progress ?></div><div style="display:inline-block">%</div></strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({trigger: "hover"});
    });
</script>