<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
$classHandler = new ClassHandler();
$schoolHandler = new SchoolHandler();
$schoolHandler->get_all_schools();
if ($classHandler->_user->user_type_id != 1) {
    $classHandler->get_classes_by_school_id($classHandler->_user->school_id);
} else {
    $classHandler->get_all_classes();
}
?>
<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="find_class_header"><a href="#tab-1" class="my_tab_header" id="find_class_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_CLASS"); ?></a></li>
                    <li role="presentation" id="edit_class_header" class=""><a href="#tab-2" class="my_tab_header" id="edit_class_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("EDIT_CLASS_GENERIC"); ?></a></li>
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
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                    </tr>
                                </tfoot>
                                <tbody> 
                                    <?php
                                    $i = 0;
                                    foreach ($classHandler->classes as $value) {
                                        $i++;
                                        ?>

                                        <tr class="clickable_row">
                                            <td class="click_me" target="class_title" title="<?php echo $value->title; ?>"><?php echo $value->title; ?></td>
                                            <td class="click_me" target="class_description" title="<?php echo $value->description; ?>"><?php echo $value->description; ?></td>
                                            <td class="click_me" target="class_year" title="<?php echo $value->class_year; ?>"><?php echo $value->class_year; ?></td>
                                            <td class="click_me" target="class_begin" title="<?php echo $value->start_date; ?>"><?php echo $value->start_date; ?></td>
                                            <td class="click_me" target="class_end" title="<?php echo $value->end_date; ?>"><?php echo $value->end_date; ?></td>
                                            <td hidden target="update_class_id" title="<?php echo $value->id; ?>"></td>
                                            <td class="" target="open" title="<?php echo $value->open; ?>">
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
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="my_fade my_tab" id="tab-2">
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
                                            <input class="checkbox-circle checkbox-dark" checked="" type="checkbox" name="class_open_checkbox" id="class_open">
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
<script src="js/my_tab.js" type="text/javascript"></script>
<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".datepickers").datepicker({
        dateFormat: "yy-mm-dd"
    });
</script>
