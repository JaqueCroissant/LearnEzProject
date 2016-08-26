<?php
require_once 'require.php';
require_once '../../include/handler/homeworkHandler.php';

$current_user = SessionKeyHandler::get_from_session("user", true);
$homeworkHandler = new HomeworkHandler();

if ($current_user->user_type_id == 1) {
    ErrorHandler::show_error_page($homeworkHandler->error);
    die();
}

if (!$homeworkHandler->get_homework(isset($_GET["homework_id"]) ? $_GET["homework_id"] : null)) {
    ErrorHandler::show_error_page($homeworkHandler->error);
    die();
}

$current_homework = $homeworkHandler->specific_homework;
$incomplete_lectures = 0;
$incomplete_tests = 0;

?>

<div class="profile-header" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem !important;background: #fff;padding: 64px 0px 0px 0px;">
    <div class="center">
        <div class="calendar-homework-big animated shake" style="margin:0px auto 0px auto;background: <?= $current_homework->color; ?>;">!</div>
        <h4 class="profile-info-name m-b-xl m-t-lg"><span class="title-color"><?= $current_homework->title; ?></span></h4>
    </div>

    <div class="row" style="margin:0px !important;">
        <div class="col-md-5 col-center">
            <div class="fc-toolbar">
                <table style="width:100%;">
                    <tr>
                        <td style="text-align:left;font-size:15px;font-weight:600;"><?= TranslationHandler::get_static_text("RELATED_CLASSES") ?>:</td>
                        <td style="text-align:right;font-size:15px;font-weight:600;"><?= TranslationHandler::get_static_text("DATE_CREATED") ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;padding-bottom:15px;">
                            <?php
                            $classes = "";
                            for ($i = 0; $i < count($current_homework->classes); $i++) {
                                $classes .= $current_homework->classes[$i]->title;
                                $classes .= $i != count($current_homework->classes) - 1 ? ", " : "";
                            }
                            ?>
                            <span data-toggle="tooltip" title="<?= $classes ?>"><?= strlen($classes) > 40 ? substr($classes, 0, 40) . "..." : $classes ?></span>
                        </td>
                        <td style="text-align:right;padding-bottom:15px;"><?= $current_homework->date_assigned; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;font-size:15px;font-weight:600;" colspan="2"><?= TranslationHandler::get_static_text("MESSAGE") ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;" colspan="2"><?= empty($current_homework->description) ? TranslationHandler::get_static_text("NO_HOMEWORK_MESSAGE") : $current_homework->description; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="promo-footer" >
        <div class="row no-gutter">
            <div class="col-sm-2 col-sm-offset-3 col-xs-6 promo-tab">
                <div class="text-center">
                    <small><?= TranslationHandler::get_static_text("DEADLINE") ?></small>
                    <h4 class="m-0 m-t-xs"><?= $current_homework->date_expire ?></h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-6 promo-tab">
                <div class="text-center">
                    <small><?= TranslationHandler::get_static_text("CREATED_BY") ?></small>             
                    <h4 class="m-0 m-t-xs change_page" page="account_profile" args="&user_id=<?= $current_homework->user_id ?>" style="cursor:pointer;"><?= format_first_last_name($current_homework->firstname, $current_homework->surname, 40) ?></h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-12 promo-tab">
                <div class="text-center">
                    <small><?= TranslationHandler::get_static_text("EXPIRES_IN") ?></small>
                    <?php
                    $date_difference = time() - strtotime($current_homework->date_expire);
                    $date_until = floor($date_difference / (60 * 60 * 24)) < 0 ? floor($date_difference / (60 * 60 * 24)) * -1 : (strtotime($current_homework->date_expire) == strtotime(date("Y-m-d")) ? -1 : 0);
                    ?>
                    <h4 class="m-0 m-t-xs" style="color: <?= $date_until > 1 ? "#36ce1c" : ($date_until > 0 || $date_until == -1 ? "#f3c02c" : "#f15530") ?>"><?= ($date_until > 0 ? ($date_until . " ") : "") ?><?= ($date_until > 1 ? TranslationHandler::get_static_text("DATE_DAYS") : ($date_until > 0 ? TranslationHandler::get_static_text("DATE_DAY") : ($date_until == -1 ? TranslationHandler::get_static_text("TODAY") : TranslationHandler::get_static_text("EXPIRED")))) ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>

<?php if ($current_user->user_type_id == 4) { ?>
    <div class="row">
        <div class="col-md-9">
            <div class="col-md-12" style="padding-right:0.25rem;padding-left: 0.25rem;">
                <div class="panel panel-default">
                    <div class="panel-heading p-h-lg p-v-md" >
                        <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-movie zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("LECTURES") ?></h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body user-description">
                        <?php if (!empty($current_homework->lectures)) { ?>
                            <div class="incomplete-homework">
                                <table id="classes" class="table display table-hover" data-plugin="DataTable"  data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [2, 3, 4]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json" : "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                    <thead>
                                        <tr>
                                            <th><?= TranslationHandler::get_static_text("TITLE") ?></th>
                                            <th><?= TranslationHandler::get_static_text("COURSE") ?></th>
                                            <th style='text-align:center;'><?= TranslationHandler::get_static_text("POINT_AMOUNT") ?></th>
                                            <th style='text-align:center;'><?= TranslationHandler::get_static_text("DIFFICULTY") ?></th>
                                            <th style='text-align:center;'><?= TranslationHandler::get_static_text("STATUS") ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($current_homework->lectures as $value) {
                                            if (!$value->is_complete) {
                                                $incomplete_lectures++;
                                            }
                                            ?>
                                            <tr class="a play_lecture" element_id="<?= $value->id ?>" data-container="body" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="<?= $value->description ?>">
                                                <td><span data-toggle="tooltip" title="<?= $value->title ?>"><?= strlen($value->title) > 40 ? substr($value->title, 0, 40) . "..." : $value->title ?></span></td>
                                                <td><?php echo $value->course_title; ?></td>
                                                <td style="text-align:center;"><?php echo $value->points; ?></td>
                                                <td style='text-align:center;'><?php echo $value->advanced ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD") ?></td>
                                                <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' .TranslationHandler::get_static_text("INCOMPLETE") .'"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="'.TranslationHandler::get_static_text("COMPLETE").'"></i>' ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> '. TranslationHandler::get_static_text("NO_HOMEWORK_LECTURES") .'</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12" style="padding-right:0.25rem;padding-left: 0.25rem;">
                <div class="panel panel-default">
                    <div class="panel-heading p-h-lg p-v-md" >
                        <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-graduation-cap zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("TESTS") ?></h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body user-description">
                        <?php if (!empty($current_homework->tests)) { ?>
                            <div class="incomplete-homework">
                                <table id="classes" class="table display table-hover" data-plugin="DataTable"  data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [2, 3, 4]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json" : "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                    <thead>
                                        <tr>
                                            <th><?= TranslationHandler::get_static_text("TITLE") ?></th>
                                            <th><?= TranslationHandler::get_static_text("COURSE") ?></th>
                                            <th style='text-align:center;'><?= TranslationHandler::get_static_text("POINT_AMOUNT") ?></th>
                                            <th style='text-align:center;'><?= TranslationHandler::get_static_text("DIFFICULTY") ?></th>
                                            <th style='text-align:center;'><?= TranslationHandler::get_static_text("STATUS") ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($current_homework->tests as $value) {
                                            if (!$value->is_complete) {
                                                $incomplete_tests++;
                                            }
                                            ?>
                                            <tr class="a change_page" page="course_show" args="&course_id=<?= $value->course_id ?>" data-container="body" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="<?= $value->description ?>">
                                                <td><span data-toggle="tooltip" title="<?= $value->title ?>"><?= strlen($value->title) > 40 ? substr($value->title, 0, 40) . "..." : $value->title ?></span></td>
                                                <td><?php echo $value->course_title; ?></td>
                                                <td style="text-align:center;"><?php echo $value->points; ?></td>
                                                <td style='text-align:center;'><?php echo $value->advanced ? TranslationHandler::get_static_text("EASY") : TranslationHandler::get_static_text("HARD") ?></td>
                                                <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="'.TranslationHandler::get_static_text("INCOMPLETE").'"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="'.TranslationHandler::get_static_text("COMPLETE").'"></i>' ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> '. TranslationHandler::get_static_text("NO_HOMEWORK_TESTS") .'</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading p-h-lg p-v-md">
                    <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("STATUS") ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body">
                    <?= ($incomplete_lectures > 0 || $incomplete_tests > 0) ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg" style="color: #f15530;margin: 10px auto 40px auto; display: block; text-align: center;font-size: 130px;width: auto !important;" data-toggle="tooltip" title="'.TranslationHandler::get_static_text("INCOMPLETE").'"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg" style="color: #36ce1c;margin: 10px auto 40px auto; display: block; text-align: center;font-size: 130px;width: auto !important;" data-toggle="tooltip" title="'.TranslationHandler::get_static_text("COMPLETE").'"></i>' ?>
                    <table class="profile_information_table">
                        <tr>
                            <td><?= TranslationHandler::get_static_text("INCOMPLETE_LECTURES") ?>:</td>
                            <td style="text-align:right;"><?= $incomplete_lectures ?></td>
                        </tr>
                        <tr>
                            <td><?= TranslationHandler::get_static_text("INCOMPLETE_TESTS") ?>:</td>
                            <td style="text-align:right;"><?= $incomplete_tests ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="row">
        <div class="col-md-9">
            <?php foreach ($current_homework->classes as $class) { ?>
                <div class="col-md-12 accordion" id="accordion" style="padding-right:0.25rem;padding-left: 0.25rem;">
                    <div id="class-<?= $class->id ?>" class="panel panel-default">
                        <div class="panel-heading p-h-lg p-v-md switcher switcher-<?= $class->id ?>" switcher_id="<?= $class->id ?>" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-class-<?= $class->id ?>" aria-expanded="true" aria-controls="collapse-class-<?= $class->id ?>">
                            <h4 class="panel-title" style="text-transform: none !important;float:left;"><i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg" style="padding-right:30px;"></i><?= $class->title ?></h4>
                            <i class="zmdi zmdi-hc-lg zmdi-minus switch_me" style="float:right;cursor:pointer;padding-top:2px;padding-right:30px;"></i>
                            <div style="clear:both;"></div>
                        </div>

                        <hr class="widget-separator m-0">
                        <div class="panel-body user-description panel-collapse collapse in" id="collapse-class-<?= $class->id ?>"  role="tabpanel">
                            <?php
                            if (empty($class->students)) {
                                echo '<div class="center latest-homework-empty" class_id="' . $class->id . '" style="margin-top:20px;margin-bottom:20px;"> '. TranslationHandler::get_static_text("NO_HOMEWORK_STUDENTS") .'</div>';
                            } else {
                                ?>
                                <div class="incomplete-homework">
                                    <table class="my_data_table table display table-hover" class_id="<?= $class->id ?>" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [2,3]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json" : "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                        <thead>
                                            <tr>
                                                <th><?= TranslationHandler::get_static_text("NAME") ?></th>
                                                <th><?= TranslationHandler::get_static_text("USERNAME") ?></th>
                                                <th style='text-align:center;'><?= TranslationHandler::get_static_text("CONTACT") ?></th>
                                                <th style='text-align:center;'><?= TranslationHandler::get_static_text("STATUS") ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($class->students as $value) { ?>
                                                <tr class="a change_page" page="account_profile" args="&user_id=<?= $value->id ?>">
                                                    <td><span data-toggle="tooltip" title="<?= $value->firstname . ' ' . $value->surname ?>"><?= format_first_last_name($value->firstname, $value->surname, 40) ?></span></td>
                                                    <td><?php echo $value->username; ?></td>
                                                    <td style='text-align:center;'><i class="fa fa-envelope change_page" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>" page="mail" step="create_mail" args="&receiver_id=USER_ANY_<?= $value->id ?>"></i></td>
                                                    <td style='text-align:center;' data-container="body" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                        <?php
                                                        if(!empty($value->lectures)) {
                                                            echo '<b>'.TranslationHandler::get_static_text("LECTURES").':</b>';
                                                            foreach($value->lectures as $lecture) {
                                                                echo '<br />- ' . $lecture->title . ' (' . ($lecture->is_complete ? '<b>'.TranslationHandler::get_static_text("COMPLETE").'</b>' : '<b>'.TranslationHandler::get_static_text("INCOMPLETE").'</b>') . ')';
                                                            }
                                                            echo '<br />';
                                                        }

                                                        if(!empty($value->tests)) {
                                                            echo '<b>'.TranslationHandler::get_static_text("TESTS").':</b>';
                                                            foreach($value->tests as $test) {
                                                                echo '<br />- ' . $test->title . ' (' . ($test->is_complete ? '<b>'.TranslationHandler::get_static_text("COMPLETE").'</b>' : '<b>'.TranslationHandler::get_static_text("INCOMPLETE").'</b>') . ')';
                                                            }
                                                        }

                                                        ?>"><?= !$value->homework_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;"></i>' ?></td>
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
                        <?php foreach ($current_homework->classes as $class) { ?>
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
        <div id="delete_text"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_alertbox_btn" page="homework_show" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover({trigger: "hover"});

        $(document).on("click", ".go-to-class", function () {
            var current_id = $(this).attr("class_id");
            var elems = $(".switcher");
            var count = elems.length;
            var current = 0;
            elems.each(function () {
                current = current + 1;
                var switcher_id = $(this).attr("switcher_id");
                if ($("#collapse-class-" + switcher_id).hasClass("in")) {
                    $(this).trigger("click");
                }

                if (current === count) {
                    setTimeout(function () {
                        $(".switcher-" + current_id).trigger("click");
                        $('html, body').animate({
                            scrollTop: $("#class-" + current_id).offset().top
                        }, 1000);
                    }, 500);

                }

            });
        });
        
        var count = $('.latest-homework-empty').length + $('.table').length;
        if(count > 1) {
            $(".latest-homework-empty").each(function () {
                var class_id = $(this).attr("class_id");
                $(".switcher-" + class_id).trigger("click");
            });

            $(".table").on("init.dt", function () {
                var class_id = $(this).attr("class_id");
                $(".switcher-" + class_id).trigger("click");
            });
        }
    });
</script>