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
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="find_school_header"><a href="#find_school_tab" class="my_tab_header" id="find_school_a" data-toggle="tab">
                            <?php echo TranslationHandler::get_static_text("FIND_SCHOOL"); ?></a></li>
                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                        <li role="presentation" id="edit_school_header" class="hidden"><a href="#edit_school_tab" class="my_tab_header" id="edit_school_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("SCHOOL_EDIT"); ?></a></li>
                    <?php } ?>
                </ul><!-- .nav-tabs -->

                <div class="my_tab_content">
                    <!-- Tab -->
                    <div class="my_fade my_tab" id="find_school_tab">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable" cellspacing="0" data-plugin="DataTable" role="grid" 
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1" aria-sort="ascending"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                        <th class="sorting p-r-0" tabindex="0" aria-controls="default-datatable"><?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th class="sorting p-r-0" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></th>
                                        <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                            <?php if (RightsHandler::has_user_right("SCHOOL_RIGHTS")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("RIGHTS"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("SCHOOL_DELETE")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("DELETE"); ?></th>
                                        <?php } ?>
                                        <th hidden tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"></th>
                                    </tr>
                                </thead>
                                <tfoot class="hidden">
                                    <tr>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                        <th class="p-r-0"><?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></th>
                                        <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("SCHOOL_RIGHTS")) { ?>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("RIGHTS"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("SCHOOL_DELETE")) { ?>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("DELETE"); ?></th>
                                        <?php } ?>
                                        <th hidden rowspan="1" colspan="1"></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("SCHOOL_FIND")) {
                                        $i = 0;
                                        foreach ($schoolHandler->all_schools as $value) {
                                            $i++;
                                            ?>
                                            <tr class="clickable_row">
                                                <td class="click_me"><?php echo $value->name; ?></td>
                                                <td class="click_me"><?php echo $value->address; ?></td>
                                                <td class="p-r-0 click_me"><?php echo $value->zip_code; ?></td>
                                                <td class="click_me"><?php echo $value->city; ?></td>
                                                <td class="click_me"><?php echo $value->phone; ?></td>
                                                <td class="click_me"><?php echo $value->email; ?></td>
                                                <td class="click_me"><?php echo $value->school_type; ?></td>
                                                <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                    <td>
                                                        <form method="post">
                                                            <div class="p-l-0" id="school_edit_<?php echo $i; ?>_div">
                                                                <input type="text" class="school_id_hidden" hidden value="<?php echo $value->id; ?>" name="school_id" id="school_id_hidden_<?php echo $i; ?>">
                                                                <input class="btn-danger edit_school" type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("EDIT") ?>">
                                                            </div>
                                                        </form>
                                                    </td>
                                                <?php } ?>
                                                <?php if (RightsHandler::has_user_right("SCHOOL_RIGHTS")) { ?>
                                                    <td>
                                                        <form method="post">
                                                            <div class="p-l-0" id="school_edit_rights_<?php echo $i; ?>_div">
                                                                <input type="text" class="school_id_hidden" hidden value="<?php echo $value->id; ?>" name="school_id" id="school_id_hidden_<?php echo $i; ?>">
                                                                <input class="btn-danger edit_school_rights" type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("EDIT_RIGHTS") ?>">
                                                            </div>
                                                        </form>
                                                    </td>
                                                <?php } ?>
                                                <?php if (RightsHandler::has_user_right("SCHOOL_DELETE")) { ?>
                                                    <td>
                                                        <form method="post" action="" url="find_school.php" id="school_delete_<?php echo $i; ?>">
                                                            <div class="p-l-0" id="school_delete_<?php echo $i; ?>_div">
                                                                <input type="text" class="school_id_hidden" hidden value="<?php echo $value->id; ?>" name="school_id" id="school_id_delete_hidden_<?php echo $i; ?>">
                                                                <input type="text" class="school_delete_hidden" hidden value="1" name="delete_school">
                                                                <input type="hidden" name="state" value="delete_school">
                                                                <input class="btn-danger delete_school" id="school_delete_<?php echo $i; ?>_btn" type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("DELETE") ?>">
                                                            </div>
                                                        </form>
                                                    </td>
                                                <?php } ?>
                                                <td class="hidden"><?php echo $value->id; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Tab -->

                    
                    <!-- Tab -->
                    <div class="my_fade my_tab" id="edit_school_tab">
                        <div class="widget-body">
                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                            <form method="post" id="update_school_step_one" action="" name="update_school_step_one" class="form-horizontal" url="find_school.php">
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control" id="edit_school_name" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control" id="edit_school_address" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("ZIP_CODE") . " & " . TranslationHandler::get_static_text("CITY"); ?></label>
                                    <div class="col-md-1">
                                        <input class="form-control" id="edit_school_zip_code" type="text" name="school_zip_code" placeholder="<?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="edit_school_city" type="text" name="school_city" placeholder="<?php echo TranslationHandler::get_static_text("CITY"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control" id="edit_school_phone" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control" id="edit_school_email" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_max_students"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control" id="edit_school_max_students" type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>">
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_start"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control datepickers"  id="edit_school_subscription_start" type="text" name="school_subscription_start" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>">
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control datepickers" id="edit_school_subscription_end" type="text" name="school_subscription_end" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                                    <div class="col-md-5">
                                        <select name="school_type_id" id="edit_school_type_id" class="form-control" data-plugin="select2">
                                            <?php
                                            foreach ($schoolHandler->school_types as $value) {
                                                echo '<option id="school_type_id_' . $value['id'] . '" value="' . $value['id'] . '">' . $value['title'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label"></label>
                                    <div class="col-md-5">
                                        <input type="hidden" id="update_school_id" name="school_id">
                                        <input type="hidden" name="state" value="update_school">
                                        <input type="button" name="submit" id="update_school_step_one_button"
                                               value="<?php echo TranslationHandler::get_static_text("SCHOOL_UPDATE"); ?>" class="pull-right btn btn-default btn-sm update_school">   
                                    </div>
                                </div>
                            </form>
                            <?php } else { echo ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title; } ?>
                        </div>
                    </div>
                    <!-- End Tab -->
                    
                    
                    <!-- Tab -->
                    <div class="my_fade my_tab" id="edit_school_rights_tab">
                        <div class="widget-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<script src="js/school.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".datepickers").datepicker({
        dateFormat: "yy-mm-dd"
    });
</script>
