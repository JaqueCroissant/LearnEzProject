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
                    <li role="presentation" class="active"><a href="#find-school-tab-1" role="tab" data-toggle="tab" 
                                                              aria-expanded="true"><?php echo TranslationHandler::get_static_text("FIND_CLASS"); ?></a></li>
                    <li role="presentation" class=""><a href="#find-school-tab-2" role="tab" data-toggle="tab" 
                                                        aria-expanded="false"><?php echo TranslationHandler::get_static_text("EDIT_CLASS_GENERIC"); ?></a></li>
                </ul><!-- .nav-tabs -->

                <!-- Tab panes -->
                <div class="tab-content p-md">
                    <div role="tabpanel" class="tab-pane fade active in" id="find-school-tab-1">
                        <div class="widget-body">
                            <table id="default-datatable" class="table table-striped dataTable" cellspacing="0" data-plugin="DataTable" role="grid" 
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

                                        <tr>
                                            <td>
                                                <?php echo $value->title; ?>
                                                <input type="hidden" value="<?php echo $value->title; ?>" name="class_title">
                                            </td>
                                            <td><?php echo $value->description; ?></td>
                                            <td><?php echo $value->class_year; ?></td>
                                            <td><?php echo $value->start_date; ?></td>
                                            <td><?php echo $value->end_date; ?></td>
                                            <td>
                                                <form method="post" id="class_open_<?php echo $i; ?>" action="" url="find_class.php">
                                                    <div class="checkbox" id="class_open_<?php echo $i; ?>_div">
                                                        <input type="text" hidden value="<?php echo $value->id; ?>" name="class_id" id="class_open_<?php echo $i; ?>_id_hidden">
                                                        <input type="text" hidden value="<?php echo $value->open; ?>" name="class_open" id="class_open_<?php echo $i; ?>_hidden">
                                                        <!-- checkboxen skal tjekke om $value->open er 1 eller 0 og checke af hvis den er 1-->
                                                        <input class="checkbox-circle checkbox-dark btn_class_open" id="class_open_<?php echo $i; ?>_field" type="checkbox" <?php echo ($value->open == 1 ? 'checked' : "") ?>>
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

                    <div role="tabpanel" class="tab-pane fade" id="find-school-tab-2">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>