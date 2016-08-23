<?php
require_once 'require.php';
require_once '../../include/handler/calendarHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$current_user = SessionKeyHandler::get_from_session("user", true);

if ($current_user->user_type_id == 1) {
    ErrorHandler::show_error_page($homeworkHandler->error);
    die();
}

$selected_date = isset($_GET["selected_date"]) ? $_GET["selected_date"] : 0;
$homeworkHandler = new HomeworkHandler();
$calendarHandler = new CalendarHandler($selected_date);

$homeworkHandler->get_user_homework($calendarHandler->first_day_to_show, $calendarHandler->last_day_to_show);
$calendarHandler->assign_date_content($homeworkHandler->homework);

$homeworkHandler->get_user_homework();
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
    $maximum_content_elements = 0;
    foreach($calendarHandler->current_dates as $key => $value) {
        if($key == 0 || $key % 7 == 0) {
            echo '<div class="row calendar-element-row" style="margin:0px !important;"><div class="col-md-9 col-center">';
        }
        
        echo '<div class="calendar-element" data-toggle="tooltip" data-trigger="hover" title="'. $value->day_title .' - '. $value->day .' '. $value->month_title .'">
                <div class="calendar-element-container '. ($value->is_today ? 'calendar-element-container-today' : '') .' '. (!$value->in_current_month ? 'calendar-element-disabled' : '') .'">
                    <div class="calendar-element-date">'. $value->day .'</div>
                    '. (RightsHandler::has_user_right("HOMEWORK_CREATE") ? '<div class="calendar-element-create-homework change_page" page="create_homework" args="&date='. $value->full_date .'" style="visibility:hidden;" data-toggle="tooltip" data-trigger="manual" title="Opret lektie"><i class="zmdi zmdi-plus" style="display:initial !important;"></i></div>' : '') .'
                    <div style="clear:both;"></div>
                    <div class="calendar-element-content">';
        if(!empty($value->content)) {
            foreach($value->content as $content) {
                echo '<div class="calendar-homework change_page" page="homework_show" args="&homework_id='. $content->id .'" data-toggle="tooltip" data-trigger="manual" title="'.$content->title.'"  style="background: '. $content->color .';display: inline-block;float:right;cursor:pointer;">!</div>';
            }
        }
        echo        '<div style="clear:both;"></div>
                    </div>
                </div>
            </div>';
        
        if(($key+1) % 7 == 0 ) {
            echo '<div style="clear:both;"></div></div></div>';
        }
    }
    ?>
</div>


<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>

<?php if($current_user->user_type_id == 4) { ?>
<div class="row">
    <div class="col-md-9">
        <div class="col-md-12" style="padding-right:0.25rem;padding-left: 0.25rem;">
            <div class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md" >
                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-assignment-o zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("LATEST_HOMEWORK") ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body">
                    <?php if(empty($homeworkHandler->homework)) {
                        echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> '. TranslationHandler::get_static_text("NO_HOMEWORK_AT_THE_MOMENT") .'</div>';
                    } else {
                    ?>
                        <div class="latest-homework">
                        <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                            <thead>
                                <tr>
                                    <th><?= TranslationHandler::get_static_text("TITLE") ?></th>
                                    <th><?= TranslationHandler::get_static_text("CLASSES") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("END") ?> <?= strtolower(TranslationHandler::get_static_text("DATE_DATE")) ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("LECTURES") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("TESTS") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("STATUS") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($homeworkHandler->homework as $value) {
                                    $classes = "";
                                    for($i = 0; $i < count($value->classes); $i++) {
                                        $classes .= $value->classes[$i]->title;
                                        $classes .= $i != count($value->classes)-1 ? ", " : "";
                                    }

                                    ?>
                                    <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                        <?php
                                        if(!empty($value->lectures)) {
                                            echo '<b>'. TranslationHandler::get_static_text("LECTURES") .':</b>';
                                            foreach($value->lectures as $lecture) {
                                                echo '<br />- ' . $lecture->title . '';
                                            }
                                            echo '<br />';
                                        }

                                        if(!empty($value->tests)) {
                                            echo '<b>'. TranslationHandler::get_static_text("TESTS") .':</b>';
                                            foreach($value->tests as $test) {
                                                echo '<br />- ' . $test->title . '';
                                            }
                                        }

                                        ?>">
                                        <td><?php echo $value->title; ?></td>
                                        <td><span data-toggle="tooltip" title="<?= $classes ?>"><?= strlen($classes) > 40 ? substr($classes, 0, 40) . "..." : $classes ?></span></td>
                                        <td style='text-align:center;'><?php echo $value->date_expire; ?></td>
                                        <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                        <td style='text-align:center;'><?= count($value->tests) ?></td>
                                        <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="'. TranslationHandler::get_static_text("INCOMPLETE") .'"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="'. TranslationHandler::get_static_text("COMPLETE") .'"></i>' ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
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
                    <?php if(empty($homeworkHandler->incomplete_homework)) {
                        echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> '. TranslationHandler::get_static_text("NO_INCOMPLETE_HOMEWORK_AT_THE_MOMENT") .'</div>';
                    } else {
                    ?>
                        <div class="incomplete-homework">
                        <table id="classes" class="table display table-hover" data-plugin="DataTable"  data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                            <thead>
                                <tr>
                                    <th><?= TranslationHandler::get_static_text("TITLE") ?></th>
                                    <th><?= TranslationHandler::get_static_text("CLASSES") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("END") ?> <?= strtolower(TranslationHandler::get_static_text("DATE_DATE")) ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("LECTURES") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("TESTS") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("STATUS") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($homeworkHandler->incomplete_homework as $value) {
                                    $classes = "";
                                    for($i = 0; $i < count($value->classes); $i++) {
                                        $classes .= $value->classes[$i]->title;
                                        $classes .= $i != count($value->classes)-1 ? ", " : "";
                                    }

                                    ?>
                                    <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="
                                        <?php
                                        if(!empty($value->lectures)) {
                                            echo '<b>'. TranslationHandler::get_static_text("LECTURES") .':</b>';
                                            foreach($value->lectures as $lecture) {
                                                echo '<br />- ' . $lecture->title . '';
                                            }
                                            echo '<br />';
                                        }

                                        if(!empty($value->tests)) {
                                            echo '<b>'. TranslationHandler::get_static_text("TESTS") .':</b>';
                                            foreach($value->tests as $test) {
                                                echo '<br />- ' . $test->title . '';
                                            }
                                        }

                                        ?>">
                                        <td><?php echo $value->title; ?></td>
                                        <td><span data-toggle="tooltip" title="<?= $classes ?>"><?= strlen($classes) > 40 ? substr($classes, 0, 40) . "..." : $classes ?></span></td>
                                        <td style='text-align:center;'><?php echo $value->date_expire; ?></td>
                                        <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                        <td style='text-align:center;'><?= count($value->tests) ?></td>
                                        <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="'. TranslationHandler::get_static_text("INCOMPLETE") .'"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="'. TranslationHandler::get_static_text("COMPLETE") .'"></i>' ?></td>
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
    
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md">
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-info-outline zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("INFORMATION") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <table class="profile_information_table">
                    <tr>
                        <td><?= TranslationHandler::get_static_text("HOMEWORK_AMOUNT") ?>:</td>
                        <td style="text-align:right;"><?= count($homeworkHandler->homework) ?></td>
                    </tr>
                     <tr>
                        <td><?= TranslationHandler::get_static_text("COMPLETE_HOMEWORK") ?>:</td>
                        <td style="text-align:right;"><?= count($homeworkHandler->homework)- count($homeworkHandler->incomplete_homework) ?></td>
                    </tr>
                     <tr>
                        <td><?= TranslationHandler::get_static_text("INCOMPLETE_HOMEWORK") ?>:</td>
                        <td style="text-align:right;"><?= count($homeworkHandler->incomplete_homework) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="row">
    <div class="col-md-9">
    <?php foreach($homeworkHandler->classes as $class) { ?>
        <div class="col-md-12 accordion" id="accordion" style="padding-right:0.25rem;padding-left: 0.25rem;">
            <div id="class-<?= $class->id ?>" class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md switcher switcher-<?= $class->id ?>" switcher_id="<?= $class->id ?>" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-class-<?= $class->id ?>" aria-expanded="true" aria-controls="collapse-class-<?= $class->id ?>">
                    <h4 class="panel-title" style="text-transform: none !important;float:left;"><i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg" style="padding-right:30px;"></i><?= $class->title ?></h4>
                    <i class="zmdi zmdi-hc-lg zmdi-minus switch_me" style="float:right;cursor:pointer;padding-top:2px;padding-right:30px;"></i>
                    <div style="clear:both;"></div>
                </div>
                
                <hr class="widget-separator m-0">
                <div class="panel-body user-description panel-collapse collapse in" id="collapse-class-<?= $class->id ?>"  role="tabpanel">
                    <?php if(empty($class->homework)) {
                        echo '<div class="center latest-homework-empty" class_id="'. $class->id .'" style="margin-top:20px;margin-bottom:20px;"> '. TranslationHandler::get_static_text("CLASS_NO_HOMEWORK_AT_THE_MOMENT").'</div>';
                    } else {
                    ?>
                        <div class="incomplete-homework">
                            <table class="my_data_table table display table-hover datatable_<?= $class->id ?>" class_id="<?= $class->id ?>" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4<?= RightsHandler::has_user_right("HOMEWORK_DELETE") ? ',5' : '' ?>]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                            <thead>
                                <tr>
                                    <th><?= TranslationHandler::get_static_text("TITLE") ?></th>
                                    <th><?= TranslationHandler::get_static_text("CREATED_BY") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("END") ?> <?= strtolower(TranslationHandler::get_static_text("DATE_DATE")) ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("LECTURES") ?></th>
                                    <th style='text-align:center;'><?= TranslationHandler::get_static_text("TESTS") ?></th>
                                    <?php if(RightsHandler::has_user_right("HOMEWORK_DELETE")) { ?></th>
                                        <th style='text-align:center;'><?= TranslationHandler::get_static_text("DELETE") ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($class->homework as $value) { ?>
                                    <tr class="a" data-container="body" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="
                                        <?php
                                        if(!empty($value->lectures)) {
                                            echo '<b>'. TranslationHandler::get_static_text("LECTURES") . ':</b>';
                                            foreach($value->lectures as $lecture) {
                                                echo '<br />- ' . $lecture->title . '';
                                            }
                                            echo '<br />';
                                        }

                                        if(!empty($value->tests)) {
                                            echo '<b>'. TranslationHandler::get_static_text("TESTS") . ':</b>';
                                            foreach($value->tests as $test) {
                                                echo '<br />- ' . $test->title . '';
                                            }
                                        }

                                        ?>">
                                        <td class="change_page" page="homework_show" args="&homework_id=<?= $value->id ?>"><?php echo $value->title; ?></td>
                                        <td class="change_page" page="homework_show" args="&homework_id=<?= $value->id ?>"><span data-toggle="tooltip" title="<?= $value->firstname. ' ' . $value->surname ?>"><?= strlen($value->firstname. ' ' . $value->surname) > 40 ? substr($value->firstname. ' ' . $value->surname, 0, 40) . "..." : $value->firstname. ' ' . $value->surname ?></span></td>
                                        <td class="change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" style='text-align:center;'><?php echo $value->date_expire; ?></td>
                                        <td class="change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" style='text-align:center;'><?= count($value->lectures) ?></td>
                                        <td class="change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" style='text-align:center;'><?= count($value->tests) ?></td>
                                        <?php if(RightsHandler::has_user_right("HOMEWORK_DELETE")) { ?></th>
                                        <td style='text-align:center;'>
                                        <?php if($current_user->id == $value->user_id) { ?>
                                            <form style="display: inline-block;" method="post" id="click_alert_form_<?php echo $value->id; ?>" url="homework.php?step=delete_homework">
                                                <input type="hidden" name="homework_id" value="<?php echo $value->id; ?>">
                                                <i class="zmdi zmdi-hc-lg zmdi-delete btn_click_alertbox" current_datatable="datatable_<?= $class->id ?>" element_id="<?php echo $value->id; ?>" id="click_alert_btn" style="" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("DELETE")?>"></i>
                                                <input type="hidden" name="submit" value="submit"></input>
                                            </form>
                                        </td>
                                        <?php } } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md">
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("CLASSES") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body">
                <table class="profile_information_table">
                    <?php foreach($homeworkHandler->classes as $class) { ?>
                    <tr>
                        <td><a href="javascript:void(0)" class="go-to-class" class_id="<?= $class->id; ?>"><?= $class->title ?></a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div id="click_alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div id="delete_text"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK_SINGULAR")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_alertbox_btn" page="homework_overview" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
    $('[data-toggle="popover"]').popover({trigger: "hover"});
    
    $('.calendar-element-row').each(function() {
        var maxHeight = -1;
        var elements = $(this).find(".calendar-element-container");
        elements.each(function() {
            
            maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
        });
        
        elements.each(function() {
            $(this).height(maxHeight);
        });
   });
   
   $(document).on("click", ".go-to-class", function() {
       var current_id = $(this).attr("class_id");
       var elems = $(".switcher");
       var count = elems.length;
       var current = 0;
       elems.each(function() {
           current = current + 1;
           var switcher_id = $(this).attr("switcher_id");
           if($("#collapse-class-" + switcher_id).hasClass("in")) {
               $(this).trigger("click");
           }
           
            if(current === count) {
                setTimeout(function() {
                   $(".switcher-" + current_id).trigger("click");
               $('html, body').animate({
                    scrollTop: $("#class-" + current_id).offset().top
                }, 1000); 
                }, 500);
               
            }
           
       });
    });
   
   $(document).on("mouseover", ".calendar-element", function() {
       var element = $(this).find(".calendar-element-create-homework");
       element.removeAttr('style').css("visibility","visibile");
   });
   
   $(document).on("mouseleave", ".calendar-element", function() {
       var element = $(this).find(".calendar-element-create-homework");
       element.removeAttr('style').css("visibility","hidden");
   });
   
   $(document).on("mouseover", ".calendar-element-create-homework", function() {
       $(this).tooltip("show");
       $(this).closest(".calendar-element").tooltip("hide");
   });
   
   $(document).on("mouseleave", ".calendar-element-create-homework", function() {
       $(this).tooltip("hide");
       $(this).closest(".calendar-element").tooltip("show");
   });
    
   $(document).on("mouseover", ".calendar-homework", function() {
       $(this).tooltip("show");
       $(this).closest(".calendar-element").tooltip("hide");
   });
   
   $(document).on("mouseleave", ".calendar-homework", function() {
       $(this).tooltip("hide");
       $(this).closest(".calendar-element").tooltip("show");
   });
   
   $(document).on("click", ".switcher", function() {
       var icon = $(this).find(".switch_me");
        if(icon.hasClass("zmdi-plus")) {
            icon.toggleClass("zmdi-plus zmdi-minus");
        } else {
            icon.toggleClass("zmdi-minus zmdi-plus");
        }
    });
    
    $(".latest-homework-empty").each(function() {
        var class_id = $(this).attr("class_id");
        $(".switcher-" + class_id).trigger("click");
    });
    
    $(".table").on("init.dt", function() {
        var class_id = $(this).attr("class_id");
        $(".switcher-" + class_id).trigger("click");
    });
});
</script>