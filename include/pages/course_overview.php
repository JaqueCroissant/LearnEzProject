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
            <div class="col-md-3 col-sm-6 change_page" style="cursor:pointer;" id="show_course" page="show_course">
                <div class="widget stats-widget">
                    <div class="widget-body clearfix">
                        <div class="pull-left" style="margin-left:-10px;">
                            <img style="width:70px;height:70px;" src="assets/images/thumbnails/<?php echo $value->image_filename; ?>" alt="">
                        </div>
                        <div class="pull-right" style="text-align:right !important;">
                            <h3 class="widget-title text-primary" style="color: <?php echo $value->color; ?> !important;font-size:18px !important;"><?php echo $value->title; ?></h3>
                            <table style="width: auto; float:right;">
                                <tr>
                                    <td><small class="text-color"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_LECTURES"); ?>:</small></td>
                                    <td style="padding-left:10px;"><small class="text-color"><?php echo $value->amount_of_lectures; ?></small></td>
                                </tr>
                                <tr>
                                    <td><small class="text-color"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_TESTS"); ?>:</small></td>
                                    <td style="padding-left:10px;"><small class="text-color"><?php echo $value->amount_of_tests; ?></small></td>
                                </tr>
                            </table>
                            
                        </div>
                    </div>
                    <footer class="widget-footer bg-primary" style="background: <?php echo $value->color; ?> !important;">
                        <span class="pull-left" style="margin-top:4px;font-weight:bold;"><?php echo $value->overall_progress; ?>%</span>
                        <span class="small-chart pull-right" ><?php echo !($value->overall_progress < 100) ? '<i class="zmdi zmdi-hc-fw zmdi-star" style="font-size:22px; color: #FFF !important;"></i>' : '<i class="zmdi zmdi-hc-fw zmdi-star" style="font-size:22px; color: #FFF !important;opacity:0;"></i>'; ?></span>

                    </footer>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
