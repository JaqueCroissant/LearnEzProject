<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
$classHandler = new ClassHandler();
$schoolHandler = new SchoolHandler();
$schoolHandler->get_all_schools();
$schoolHandler->get_school_types();
?>

<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("FIND_SCHOOL"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body">
                <?php if (RightsHandler::has_user_right("SCHOOL_FIND")) { ?>
                    <table id="default-datatable" class="table dataTable" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>, columnDefs:[{orderable: false, targets: [7,8]}], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid" 
                           aria-describedby="default-datatable_info">
                        <thead>
                            <tr role="row">
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></th>
                                <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                    <th><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                <?php } ?>
                                <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                    <th><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                <?php } ?>

                                <th hidden></th>
                            </tr>
                        </thead>
                        <tfoot class="hidden">
                            <tr>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></th>
                                <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                    <th><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                <?php } ?>
                                <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                    <th><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                <?php } ?>

                                <th hidden></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($schoolHandler->all_schools as $value) {
                                $i++;
                                ?>
                                <tr class="clickable_row">
                                    <td class="click_me"><?php echo $value->name; ?></td>
                                    <td class="click_me"><?php echo $value->address; ?></td>
                                    <td class="click_me"><?php echo $value->city; ?></td>
                                    <td class="click_me"><?php echo $value->school_type; ?></td>
                                    <td class="click_me"><?php echo $value->subscription_start; ?></td>
                                    <td class="click_me"><?php echo $value->subscription_end; ?></td>
                                    <td class="click_me"><?php echo $value->max_students; ?></td>
                                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                        <td>
                                            <form method="post" id="school_open_<?php echo $i; ?>" action="" url="edit_school.php">
                                                <div class="checkbox" id="school_open_<?php echo $i; ?>_div">
                                                    <input type="text" class="school_id_hidden" hidden value="<?php echo $value->id; ?>" name="school_id" id="school_open_<?php echo $i; ?>_id_hidden">
                                                    <input type="text" hidden value="<?php echo $value->open; ?>" name="school_open" id="school_open_<?php echo $i; ?>_hidden">
                                                    <input type="hidden" name="state" value="update_open_state">
                                                    <input class="checkbox-circle checkbox-dark btn_school_open" id="school_open_<?php echo $i; ?>_field" type="checkbox" 
                                                           <?php echo ($value->open == 1 ? 'checked' : "") ?> value="<?php echo ($value->open == 1 ? 'on' : "off"); ?>">
                                                    <label for="school_open_<?php echo $i; ?>_field"></label>
                                                    <input type='button' name="submit" hidden="">
                                                </div>
                                            </form>
                                        </td>
                                    <?php } ?>
                                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                        <td>
                                            <div class="">
                                                <i class="fa fa-edit fa-2x edit_school center" school_id="<?php echo $value->id; ?>" state="update_school" id="edit_school"></i>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td class="hidden"><?php echo $value->id; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div id="close_school_alert" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <p>
            <?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("SCHOOL")) . "?"; ?>
        </p>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_close_school_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_close_school_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<div id="open_school_alert" class="panel panel-danger alert_panel hidden">
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <p>
            <?php echo TranslationHandler::get_static_text("CONFIRM_OPEN") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("SCHOOL")) . "?"; ?>
        </p>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_close_school_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_close_school_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>