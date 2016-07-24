<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';
$userHandler = new UserHandler();
$userHandler->get_all_users();
?>

<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="find_school_header"><a href="#find_account_tab" class="my_tab_header" id="find_account_a" data-toggle="tab">
                            <?php echo TranslationHandler::get_static_text("FIND_ACCOUNT"); ?></a></li>
                    <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                        <li role="presentation" id="edit_school_header" class="hidden"><a href="#edit_account_tab" class="my_tab_header" id="edit_account_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("EDIT_ACCOUNT"); ?></a></li>
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
                                        <th class="sorting_asc" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1" aria-sort="ascending"><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th class="sorting p-r-0" tabindex="0" aria-controls="default-datatable"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th class="sorting" tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden tabindex="0" aria-controls="default-datatable" rowspan="1" colspan="1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("ACCOUNT_FIND")) {
                                        $i = 0;
                                        foreach ($userHandler->users as $value) {
                                            $i++;
                                            ?>
                                            <tr class="clickable_row">
                                                <td class="click_me"><?php echo $value->firstname . " " . $value->surname; ?></td>
                                                <td class="click_me"><?php echo $value->username; ?></td>
                                                <td class="click_me"><?php echo $value->user_type_title; ?></td>
                                                <td class="click_me"><?php echo $value->email; ?></td>
                                                <td class="click_me"><?php echo $value->school_name; ?></td>
                                                <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                                                    <td>
                                                        <form method="post" id="account_open_<?php echo $i; ?>" action="" url="find_account.php">
                                                            <div class="checkbox" id="account_open_<?php echo $i; ?>_div">
                                                                <input type="text" class="account_id_hidden" hidden value="<?php echo $value->id; ?>" name="class_id" id="account_open_<?php echo $i; ?>_id_hidden">
                                                                <input type="text" hidden value="<?php echo $value->open; ?>" name="account_open" id="account_open_<?php echo $i; ?>_hidden">
                                                                <input type="hidden" name="state" value="update_open_state">
                                                                <input class="checkbox-circle checkbox-dark btn_class_open" id="account_open_<?php echo $i; ?>_field" type="checkbox" 
                                                                       <?php echo ($value->open == 1 ? 'checked' : "") ?> value="<?php echo ($value->open == 1 ? 'on' : "off"); ?>">
                                                                <label for="account_open_<?php echo $i; ?>_field"></label>
                                                                <input type='button' name="submit" hidden="">
                                                            </div>
                                                        </form>
                                                    </td>
                                                <?php } ?>
                                                <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                                    <td>
                                                        <div class="">
                                                            <i class="fa fa-edit fa-2x edit_account m-r-md" account_id="<?php echo $value->id; ?>" state="update_account" id="edit_account_btn"></i>
                                                            <?php if (RightsHandler::has_user_right("ACCOUNT_DELETE")) { ?>
                                                                <i class="fa fa-times fa-fw fa-2x delete_account" account_id="<?php echo $value->id; ?>" state="delete_account" id="delete_account_btn" style="font-size: 2.5em !important;"></i>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
<!--                                                    <td>
                                                        <form method="post" action="" url="find_account.php" id="account_delete_<?php echo $i; ?>">
                                                            <div class="p-l-0" id="account_delete_<?php echo $i; ?>_div">
                                                                <input type="text" class="account_id_hidden" hidden value="<?php echo $value->id; ?>" name="school_id" id="account_id_delete_hidden_<?php echo $i; ?>">
                                                                <input type="text" class="account_delete_hidden" hidden value="1" name="delete_account">
                                                                <input type="hidden" name="state" value="delete_school">
                                                                <input class="btn-danger delete_account" id="account_delete_<?php echo $i; ?>_btn" type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("DELETE") ?>">
                                                            </div>
                                                        </form>
                                                    </td>-->
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
                    <div class="my_fade my_tab" id="edit_account_tab">
                        <div class="widget-body">
                            <form method="post" id="update_school_step_one" action="" name="update_school_step_one" class="form-horizontal" url="find_school.php">
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("ZIP_CODE") . " & " . TranslationHandler::get_static_text("CITY"); ?></label>
                                    <div class="col-md-1">
                                        <input class="form-control" type="text" name="school_zip_code" placeholder="<?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" type="text" name="school_city" placeholder="<?php echo TranslationHandler::get_static_text("CITY"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_max_students"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control " type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>">
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_start"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control datepickers" id="school_subscription_start" type="text" name="school_subscription_start" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>">
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                                    <div class="col-md-5">
                                        <input class="form-control datepickers" id="school_subscription_end" type="text" name="school_subscription_end" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                                    <div class="col-md-5">
                                        <select name="school_type_id" id="school_type_id" class="form-control" data-plugin="select2">
                                            //<?php
//                                            foreach ($schoolHandler->school_types as $value) {
//                                                echo '<option id="school_type_id_' . $value['id'] . '" value="' . $value['id'] . '">' . $value['title'] . '</option>';
//                                            }
//                                            
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 col-md-offset-2 control-label"></label>
                                    <div class="col-md-5">
                                        <input type="hidden" name="step" id="create_school_hidden_field_step_1">
                                        <input type="button" name="submit" id="create_school_step_one_button" step="1" 
                                               value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_ONE"); ?>" class="pull-right btn btn-default btn-sm create_school">   
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Tab -->

                    <!-- Tab -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
