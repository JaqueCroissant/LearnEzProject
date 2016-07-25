<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
$classHandler = new ClassHandler();
$schoolHandler = new SchoolHandler();
$schoolHandler->get_all_schools();
$classHandler->get_all_classes();
?>
<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("FIND_CLASS"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body">
                <table id="find_class_dt" class="table dataTable" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>, lengthMenu:[5, 10, 25, 50, 100], columnDefs:[{orderable: false, targets: [6,7]}]}"  data-plugin="DataTable" role="grid" 
                       aria-describedby="default-datatable_info">
                    <thead>
                        <tr role="row">
                            <th><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                            <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                            <?php } ?>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></th>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                            <?php } ?>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                            <?php } ?>
                            <th hidden></th>
                        </tr>
                    </thead>
                    <tfoot class="hidden">
                        <tr>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                            <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                            <?php } ?>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></th>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                            <?php } ?>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                            <?php } ?>
                            <th hidden></th>
                        </tr>
                    </tfoot>
                    <tbody> 
                        <?php
                        $i = 0;
                        foreach ($classHandler->classes as $value) {
                            $i++;
                            ?>

                            <tr class="clickable_row">

                                <td class="click_me"><?php echo $value->title; ?></td>
                                <td class="click_me"><?php echo $value->description; ?></td>
                                <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                    <td class="click_me"><?php echo $value->school_name; ?></td>
                                <?php } ?>
                                <td class="click_me"><?php echo $value->class_year; ?></td>
                                <td class="click_me"><?php echo $value->start_date; ?></td>
                                <td class="click_me"><?php echo $value->end_date; ?></td>

                                <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                    <td>
                                        <form method="post" id="class_open_<?php echo $i; ?>" action="" url="edit_class.php">
                                            <div class="checkbox" id="class_open_<?php echo $i; ?>_div">
                                                <input type="text" class="class_id_hidden" hidden value="<?php echo $value->id; ?>" name="class_id" id="class_open_<?php echo $i; ?>_id_hidden">
                                                <input type="text" hidden value="<?php echo $value->open; ?>" name="class_open" id="class_open_<?php echo $i; ?>_hidden">
                                                <input type="hidden" name="state" value="update_open_state">
                                                <input class="checkbox-circle checkbox-dark btn_class_open" id="class_open_<?php echo $i; ?>_field" type="checkbox" 
                                                       <?php echo ($value->open == 1 ? 'checked' : "") ?> value="<?php echo ($value->open == 1 ? 'on' : "off"); ?>">
                                                <label for="class_open_<?php echo $i; ?>_field"></label>
                                                <input type='button' name="submit" hidden="">
                                            </div>
                                        </form>
                                    </td>
                                <?php } ?>
                                <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                    <td>
                                        <div class="">
                                            <i class="fa fa-edit fa-fw fa-2x edit_class m-r-md a" school_id="<?php echo $value->id; ?>" state="update_school" id="edit_school"></i>
                                            <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                                                <i class="fa fa-times fa-fw fa-2x delete_class a" school_id="<?php echo $value->id; ?>" state="update_school" id="edit_school" style="font-size: 2.5em !important;"></i>
                                            <?php } ?>
                                        </div>
                                    </td>
                                <?php } ?>
                                <td hidden id="<?php echo $value->id ?>"></td>
                            </tr>

                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="close_class_alert" class="panel panel-danger alert_panel" hidden>
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <p>
            <?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE_CLASS"); ?>
        </p>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg" id="accept_close_class_btn" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg" id="cancel_close_class_btn" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<div id="open_class_alert" class="panel panel-danger alert_panel" hidden>
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <p>
            <?php echo TranslationHandler::get_static_text("CONFIRM_OPEN_CLASS"); ?>
        </p>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg" id="accept_close_class_btn" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg" id="cancel_close_class_btn" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<div id="delete_class_alert" class="panel panel-danger alert_panel" hidden>
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <p>
            <?php echo TranslationHandler::get_static_text("CONFIRM_DELETE_CLASS"); ?>
        </p>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg" id="accept_delete_class_btn" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg" id="cancel_delete_class_btn" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
