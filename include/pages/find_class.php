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
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="find_class_header"><a href="#tab-1" class="my_tab_header" id="find_class_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_CLASS"); ?></a></li>
                    <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                        <li role="presentation" id="edit_class_header" class=""><a href="#edit_class" class="my_tab_header" id="edit_class_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("EDIT_CLASS_GENERIC"); ?></a></li>
                    <?php } ?>
                </ul><!-- .nav-tabs -->

                <!-- Tab panes -->
                <div class="my_tab_content">
                    <div class="my_fade my_tab" id="tab-1">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable" cellspacing="0" data-plugin="DataTable" role="grid" 
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1" aria-sort="ascending"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                                        <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php } ?>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("DELETE"); ?></th>
                                        <?php } ?>
                                        <th hidden tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                                        <?php if ($classHandler->_user->user_type_id == 1) { ?>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php } ?>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("DELETE"); ?></th>
                                        <?php } ?>
                                        <th hidden rowspan="1" colspan="1"></th>
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

                                            <td>
                                                <form method="post" id="class_open_<?php echo $i; ?>" action="" url="find_class.php">
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
                                            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                                <td>
                                                    <form method="post">
                                                        <div class="p-l-0" id="class_edit_<?php echo $i; ?>_div">
                                                            <input type="text" class="class_id_hidden" hidden value="<?php echo $value->id; ?>" name="class_id" id="class_id_hidden_<?php echo $i; ?>">
                                                            <input class="btn-default edit_class" type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("EDIT") ?>">
                                                        </div>
                                                    </form>
                                                </td>
                                            <?php } ?>
                                            <?php if (RightsHandler::has_user_right("CLASS_DELETE")) { ?>
                                                <td>
                                                    <form method="post" action="" url="find_class.php" id="class_delete_<?php echo $i; ?>">
                                                        <div class="p-l-0" id="class_delete_<?php echo $i; ?>_div">
                                                            <input type="text" class="class_id_hidden" hidden value="<?php echo $value->id; ?>" name="class_id" id="class_id_delete_hidden_<?php echo $i; ?>">
                                                            <input type="text" class="class_delete_hidden" hidden value="1" name="delete_class">
                                                            <input type="hidden" name="state" value="delete_class">
                                                            <input class="btn-default delete_class" id="class_delete_<?php echo $i; ?>_btn" type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("DELETE") ?>">
                                                        </div>
                                                    </form>
                                                </td>
                                            <?php } ?>
                                            <td hidden user_type_id="<?php echo $classHandler->_user->user_type_id ?>" id="<?php echo $value->id ?>"></td>
                                        </tr>

                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                        <div class="my_fade my_tab" id="edit_class">
                            <div class="widget-body">
                                <form method="post" id="update_class_form" name="update_class" action="" class="form-horizontal" url="find_class.php">
                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-3 control-label" for="class_title"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></label>
                                        <div class="col-md-4">
                                            <input class="form-control" id="class_title" type="text" name="class_title" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-3 control-label" for="class_open"><?php echo TranslationHandler::get_static_text("OPEN"); ?></label>
                                        <div class="col-md-4">
                                            <div class="checkbox">
                                                <input type="hidden" name="class_open" id="class_open_hidden">
                                                <input class="checkbox-circle checkbox-dark" type="checkbox" name="class_open_checkbox" id="class_open">
                                                <label for="class_open"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-3 control-label" for="class_begin"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></label>
                                        <div class="col-md-4">
                                            <input class="form-control datepickers" type="text" id="class_begin" name="class_begin" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-3 control-label" for="class_end"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></label>
                                        <div class="col-md-4">
                                            <input class="form-control datepickers" type="text" id="class_end" name="class_end" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_END"); ?>">
                                        </div>
                                    </div>
                                    <?php
                                    if ($classHandler->_user->user_type_id == 1) {
                                        ?>
                                        <div class="form-group">
                                            <label class="col-sm-2 col-sm-offset-3 control-label" for="school_id"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                            <div class="col-md-4">
                                                <select id="select_school" name="school_id" class="form-control">
                                                    <option value="" id="school_id"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME") ?></option>
                                                    <?php
                                                    if (count($schoolHandler->all_schools) > 0) {
                                                        foreach ($schoolHandler->all_schools as $value) {
                                                            echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        echo '<input id="school_id" type="hidden" name="school_id" value="' . $classHandler->_user->school_id . '">';
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-3 control-label" for="class_description"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></label>
                                        <div class="col-md-4">
                                            <input type="hidden" id="hidden_description" name="class_description">
                                            <textarea form="update_class" class="form-control " type="text" id="class_description" placeholder="<?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?>"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-3 control-label"></label>
                                        <div class="col-md-4">
                                            <input type="hidden" id="update_class_id" name="class_id">
                                            <input type="hidden" name="state" value="update_class">
                                            <input type="button" name="submit" id="update_class"
                                                   value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>" class="pull-right btn btn-default btn-sm update_class">   
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>
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

<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".datepickers").datepicker({
        dateFormat: "yy-mm-dd"
    });
</script>
