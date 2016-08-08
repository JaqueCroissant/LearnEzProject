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
                <?php $targets = RightsHandler::has_user_right("CLASS_EDIT") ? ", targets: [6, 7]" : ""; ?>
                <table id="find_class_dt" class="table dataTable" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false<?= $targets ?>}]}" data-plugin="DataTable" role="grid"
                       aria-describedby="default-datatable_info">
                    <thead>
                        <tr role="row">
                            <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                            <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                            <?php } ?>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("BEGIN"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("END"); ?></th>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                            <?php } ?>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                            <?php } ?>
                            <th hidden></th>
                        </tr>
                    </thead>
                    <tfoot class="hidden">
                        <tr>
                            <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                            <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                            <?php } ?>
                            <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("BEGIN"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("END"); ?></th>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                            <?php } ?>
                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
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

                                <td class="change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id; ?>" data-search="<?php echo $value->title ?>"><?php echo (strlen($value->title) > 16 ? substr($value->title, 0, 16) . "..." : $value->title); ?></td>
                                <td class="change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id; ?>" data-search="<?php echo $value->description ?>"><?php echo (strlen($value->description) > 35 ? substr($value->description, 0, 35) . "..." : $value->description); ?></td>
                                <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                    <td class="change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id; ?>" data-search="<?php echo $value->school_name ?>"><?php echo (strlen($value->school_name) > 16 ? substr($value->school_name, 0, 16) . "..." : $value->school_name); ?></td>
                                <?php } ?>
                                <td class="change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id; ?>" ><?php echo $value->class_year; ?></td>
                                <td class="change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id; ?>" ><?php echo $value->start_date; ?></td>
                                <td class="change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id; ?>" ><?php echo $value->end_date; ?></td>

                                <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                    <td align="center">
                                        <form method="post" id="alert_form_<?php echo $value->id; ?>" action="" url="edit_class.php?state=set_availability">
                                            <input type="hidden" name="class_id" value="<?php echo $value->id; ?>">
                                            <div class="checkbox">
                                                <input class="checkbox-circle checkbox-dark btn_alertbox" element_id="<?php echo $value->id; ?>" type="checkbox"
                                                       <?php echo ($value->open == 1 ? 'checked' : "") ?> value="<?php echo ($value->open == 1 ? 'on' : "off"); ?>">
                                                <label></label>
                                            </div>
                                            <input type="hidden" name="submit" value="submit"></input>
                                        </form>
                                    </td>
                                <?php } ?>
                                <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                    <td align="center">
                                        <div class="">
                                            <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a" page="edit_class" step="" args="&class_id=<?php echo $value->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT_CLASS_GENERIC")?>"></i>
                                            <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                                                <i class="zmdi zmdi-hc-lg zmdi-delete delete_class a" school_id="<?php echo $value->id; ?>" state="update_school" id="edit_school" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("DELETE")?>"></i>
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

<div style="display:none;" id="open_text"><?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("CLASS")) . "?"; ?></div>
<div style="display:none;" id="close_text"><?php echo TranslationHandler::get_static_text("CONFIRM_OPEN") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("CLASS")) . "?"; ?></div>

<div id="alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>$(document).ready(function(){$("[data-toggle='tooltip']").tooltip()});</script>
