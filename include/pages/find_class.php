<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
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
                    <li role="presentation" class="active"><a href="#tab-1" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_CLASS"); ?></a></li>
                    <li role="presentation" class=""><a href="#tab-2" data-toggle="tab"><?php echo TranslationHandler::get_static_text("EDIT_CLASS_GENERIC"); ?></a></li>
                </ul><!-- .nav-tabs -->

                <!-- Tab panes -->
                <div class="tab-content p-md">
                    <div role="tabpanel" class="tab-pane fade active in" id="tab-1">
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
                                            <td class="click_me">
                                                <?php echo $value->title; ?>
                                                <input type="hidden" value="<?php echo $value->title; ?>" name="class_title">
                                            </td>
                                            <td class="click_me"><?php echo $value->description; ?></td>
                                            <td class="click_me"><?php echo $value->class_year; ?></td>
                                            <td class="click_me"><?php echo $value->start_date; ?></td>
                                            <td class="click_me"><?php echo $value->end_date; ?></td>
                                            <td class="">
                                                <form method="post" id="class_open_<?php echo $i; ?>" action="" url="find_class.php">
                                                    <div class="checkbox" id="class_open_<?php echo $i; ?>_div">
                                                        <input type="text" hidden value="<?php echo $value->id; ?>" name="class_id" id="class_open_<?php echo $i; ?>_id_hidden">
                                                        <input type="text" hidden value="<?php echo $value->open; ?>" name="class_open" id="class_open_<?php echo $i; ?>_hidden">
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

                    <div role="tabpanel" class="tab-pane fade" id="tab-2">
                        <div class="widget-body">
                            <form method="post" id="create_class_form" name="create_class" action="" url="create_class.php">
                                <div class="">
                                    <label class="control-label" for="class_title"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></label>
                                    <div class="">
                                        <input class="form-control" type="text" name="class_title" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?>">
                                    </div>
                                </div>
                                <?php
//                                if ($classHandler->_user->user_type_id == 1) {
                                    ?>
<!--                                    <div class="form-group">
                                        <label class="col-sm-2 col-sm-offset-2 control-label" for="school_id">//////<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                        <div class="col-md-5">
                                            <select id="select_school" name="school_id" class="form-control" data-plugin="select2">
                                                <?php
//                                                if (count($schoolHandler->all_schools) > 0) {
//                                                    foreach ($schoolHandler->all_schools as $value) {
//                                                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
//                                                    }
//                                                }
//                                                ?>
                                            </select>
                                        </div>
                                    </div>-->
                                    <?php
//                                } else {
//                                    echo '<input type="hidden" name="school_id" value="' . $classHandler->_user->school_id . '">';
//                                }
//                                ?>

<!--                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label" for="class_open">////<?php echo TranslationHandler::get_static_text("OPEN"); ?></label>
                                    <div class="col-md-5">
                                        <div class="checkbox">
                                            <input class="checkbox-circle checkbox-dark" checked="" type="checkbox" name="class_open" id="class_open">
                                            <label for="class_open"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label" for="class_begin">////<?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" id="class_begin" name="class_begin" placeholder="////<?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label" for="class_end">////<?php echo TranslationHandler::get_static_text("CLASS_END"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" id="class_end" name="class_end" placeholder="////<?php echo TranslationHandler::get_static_text("CLASS_END"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label" for="class_description">////<?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></label>
                                    <div class="col-md-5">
                                        <textarea form="create_class" class="form-control " type="text" id="class_description" name="class_description" placeholder="////<?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?>"></textarea>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                                    <div class="col-md-5">
                                        <input type="hidden" name="step" id="create_class_step">
                                        <input type="button" name="submit" id="create_class_step_one_button" step="1"
                                               value="////<?php echo TranslationHandler::get_static_text("CREATE_CLASS"); ?>" class="btn btn-default btn-sm create_class">   
                                    </div>
                                </div>-->

                            </form>
<!--lolllll-->
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

<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>