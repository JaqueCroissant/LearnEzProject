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
                    <?php if (RightsHandler::has_user_right("SCHOOL_FIND")) { ?>
                        <li role="presentation" id="find_school_header"><a href="#find_school_tab" class="my_tab_header" id="find_school_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_SCHOOL"); ?></a></li>
                    <?php } ?>
                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                        <li role="presentation" id="edit_school_header" class="hidden"><a href="#edit_school_tab" class="my_tab_header" id="edit_school_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("SCHOOL_EDIT"); ?></a></li>
                    <?php } ?>
                </ul><!-- .nav-tabs -->

                <div class="my_tab_content">
                    <!-- Tab -->

                    <div class="my_fade my_tab" id="find_school_tab">

                        <div class="widget-body">
                            <?php if (RightsHandler::has_user_right("SCHOOL_FIND")) { ?>
                                <table id="default-datatable" class="table dataTable" cellspacing="0" data-plugin="DataTable" role="grid" 
                                       aria-describedby="default-datatable_info">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_asc" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1" aria-sort="ascending"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></th>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></th>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></th>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></th>
                                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                            <?php } ?>
                                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                            <?php } ?>

                                            <th hidden tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"></th>
                                        </tr>
                                    </thead>
                                    <tfoot class="hidden">
                                        <tr>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></th>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></th>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></th>
                                            <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></th>
                                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                            <?php } ?>
                                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                <th rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
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
                                                    <td class="click_me"><?php echo $value->city; ?></td>
                                                    <td class="click_me"><?php echo $value->school_type; ?></td>
                                                    <td class="click_me"><?php echo $value->subscription_start; ?></td>
                                                    <td class="click_me"><?php echo $value->subscription_end; ?></td>
                                                    <td class="click_me"><?php echo $value->max_students; ?></td>
                                                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                        <td>
                                                            <form method="post" id="school_open_<?php echo $i; ?>" action="" url="find_school.php">
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
                                        } else {
                                            echo ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- End Tab -->


                    <!-- Tab -->
                    <div class="my_fade my_tab" id="edit_school_tab">
                        <div class="widget-body">
                            <?php
                            if (RightsHandler::has_user_right("SCHOOL_EDIT")) {
                                if (isset($_GET['school_id'])) {
                                    $schoolHandler->get_school_by_id($_GET['school_id']);
                                }
                                ?>
                                <form method="post" id="update_school" action="" name="update_school_step_one" class="form-horizontal" url="find_school.php">
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control" id="edit_school_name" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->name : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control" id="edit_school_address" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->address : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("ZIP_CODE") . " & " . TranslationHandler::get_static_text("CITY"); ?></label>
                                        <div class="col-md-1">
                                            <input class="form-control" id="edit_school_zip_code" type="text" name="school_zip_code" placeholder="<?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->zip_code : ""; ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" id="edit_school_city" type="text" name="school_city" placeholder="<?php echo TranslationHandler::get_static_text("CITY"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->city : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control" id="edit_school_phone" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->phone : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control" id="edit_school_email" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->email : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_max_students"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control" id="edit_school_max_students" type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->max_students : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_start"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control datepickers"  id="edit_school_subscription_start" type="text" name="school_subscription_start" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_start : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                                        <div class="col-md-5">
                                            <input class="form-control datepickers" id="edit_school_subscription_end" type="text" name="school_subscription_end" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_end : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                                        <div class="col-md-5">
                                            <select name="school_type_id" id="edit_school_type_id" class="form-control" data-plugin="select2">
                                                <?php
                                                foreach ($schoolHandler->school_types as $value) {
                                                    echo '<option ';
                                                    echo isset($_GET['school_id']) && $schoolHandler->school->school_type_id == $value['id'] ? "selected" : "";
                                                    echo ' id="school_type_id_' . $value['id'] . '" value="' . $value['id'] . '">' . $value['title'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 col-md-offset-2 control-label"></label>
                                        <div class="col-md-5">
                                            <input type="hidden" id="update_school_id" name="school_id" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->id : ""; ?>">
                                            <input type="hidden" name="state" value="update_school">
                                            <input type="button" name="submit" id="update_school_step_one_button"
                                                   value="<?php echo TranslationHandler::get_static_text("SCHOOL_UPDATE"); ?>" class="pull-right btn btn-default btn-sm <?php echo RightsHandler::has_user_right("SCHOOL_FIND") ? "update_school" : "update_school_redirect" ?>">   
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } else {
                                echo ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title;
                            }
                            ?>
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

<!--<script src="assets/js/include_library.js" type="text/javascript"></script>
--><script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/school.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".datepickers").datepicker({
        dateFormat: "yy-mm-dd"
    });
</script>
