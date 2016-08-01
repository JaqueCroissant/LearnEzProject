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
                <h4 class="panel-title">Lektioner</h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; padding: 16px 0px !important; ">

                    <?php foreach($courseHandler->lectures as $value) { ?>
                    <div class="col-md-4">
                        <div class="widget" style="cursor:pointer;">
                            <header class="widget-header" style="padding: 0.7rem 1rem !important;">
                                <?php echo $value->title; ?>
                            </header>
                            <hr class="widget-separator m-0">
                            <div class="widget-body">
                                <div class="clearfix">
                                    <div class="pull-left">
                                        <div class="pieprogress text-primary" data-plugin="circleProgress" data-value=".6" data-thickness="6" data-size="70" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .3)" data-fill="{&quot;color&quot;: &quot;#333435&quot;}">
                                            <strong style="color: #6a6c6f !important;margin-top: -14px;font-size: 16px">%60</strong>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <h3 class="m-b-xs text-right counter" data-plugin="counterUp">259</h3>
                                        <small class="text-muted">revenue today</small>
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
                <h4 class="panel-title">Tests</h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; padding: 16px 0px !important; ">

                    <?php foreach($courseHandler->tests as $value) { ?>
                    <div class="col-md-4">
                        <div class="widget" style="cursor:pointer;">
                            <header class="widget-header" style="padding: 0.7rem 1rem !important;">
                                <?php echo $value->title; ?>
                            </header>
                            <hr class="widget-separator m-0">
                            <div class="widget-body">
                                <div class="clearfix">
                                    <div class="pull-left">
                                        <div class="pieprogress text-primary" data-plugin="circleProgress" data-value=".6" data-thickness="6" data-size="70" data-start-angle="90" data-empty-fill="rgba(0, 0, 0, .3)" data-fill="{&quot;color&quot;: &quot;#333435&quot;}">
                                            <strong style="color: #6a6c6f !important;margin-top: -14px;font-size: 16px">%60</strong>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <h3 class="m-b-xs text-right counter" data-plugin="counterUp">259</h3>
                                        <small class="text-muted">revenue today</small>
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
                <h4 class="panel-title">SENEST UDFØRTE</h4>
            </div>
            <div style="border-top: 8px solid <?php echo $courseHandler->current_element->color; ?>;background: none !important; ">
                <div class="panel-body" style="background: none !important; ">
                    <p>Du har endnu ikke klaret nogen kurser eller tests.</p>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script src="assets/js/include_app.js" type="text/javascript"></script>