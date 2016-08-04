<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';
$userHandler = new UserHandler();

?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="find_account_header"><a href="#find_account_tab" class="my_tab_header" id="find_account_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_ACCOUNT"); ?></a></li>
                    <?php if (RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") || RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD")) { ?>
                        <li role="presentation" id="assign_pass_header"><a href="#assign_pass_tab" class="my_tab_header" id="assign_pass_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("ACCOUNT_ASSIGN_PASS"); ?></a></li>
                    <?php } ?>
                </ul>

                <div class="my_tab_content">
                    <div class="my_fade my_tab" id="find_account_tab">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable datatable_1" style="margin:20px 0px 25px 0px !important;" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false, targets: [5,6]}], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid"
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_AVAILABILITY")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT") || RightsHandler::has_user_right("ACCOUNT_DELETE")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden></th>
                                    </tr>
                                </thead>
                                <tfoot class="hidden">
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_AVAILABILITY")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT") || RightsHandler::has_user_right("ACCOUNT_DELETE")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden></th>
                                    </tr>
                                </tfoot>

                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("ACCOUNT_FIND")) {
                                        $userHandler->get_all_users();
                                        foreach ($userHandler->users as $value) {

                                            ?>
                                            <tr class="clickable_row account_tr_id_<?php echo $value->id; ?>">

                                                <td class="change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>" data-search="<?php echo $value->firstname . " " . $value->surname; ?>"><?php echo (strlen($value->firstname . " " . $value->surname) > 20 ? substr($value->firstname . " " . $value->surname, 0, 20) : $value->firstname . " " . $value->surname); ?></td>
                                                <td class="change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>"><?php echo $value->username; ?></td>
                                                <td class="change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>"><?php echo $value->user_type_title; ?></td>
                                                <td class="change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>" data-search="<?php echo $value->email ?>"><?php echo (strlen($value->email) > 20 ? substr($value->email, 0, 20) : $value->email); ?></td>
                                                <td class="change_page" page="account_profile" step="" args="&user_id=<?php echo $value->id; ?>" data-search="<?php echo $value->school_name ?>"><?php echo (strlen($value->school_name) > 16 ? substr($value->school_name, 0, 16) : $value->school_name); ?></td>
                                                <?php if (RightsHandler::has_user_right("ACCOUNT_AVAILABILITY")) { ?>
                                                    <td align="center">
                                                        <form method="post" id="alert_form_<?php echo $value->id; ?>" action="" url="edit_account.php?step=set_availability">
                                                            <input type="hidden" name="user_id" value="<?php echo $value->id; ?>">
                                                            <div class="checkbox">
                                                                <input class="checkbox-circle checkbox-dark btn_alertbox" element_id="<?php echo $value->id; ?>" type="checkbox"
                                                                       <?php echo ($value->open == 1 ? 'checked' : "") ?> value="<?php echo ($value->open == 1 ? 'on' : "off"); ?>">
                                                                <label></label>
                                                            </div>
                                                            <input type="hidden" name="submit" value="submit"></input>
                                                        </form>
                                                    </td>
                                                <?php } ?>
                                                <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT") || RightsHandler::has_user_right("ACCOUNT_DELETE")) { ?>
                                                    <td align="center">
                                                        <div>
                                                            <?php
                                                            if ((RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD")) || (!RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") && RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD") && $value->user_type_id > 3))
                                                            { ?>
                                                                <form style="display: inline-block;" method="post" id="click_alert_exp_form_<?php echo $value->id; ?>" url="edit_account.php?step=generate_and_insert_password">
                                                                    <input type="hidden" name="user_id[]" value="<?php echo $value->id; ?>">
                                                                    <i class="zmdi zmdi-hc-fw zmdi-lock btn_click_alertbox_pass_assign m-r-xs" element_id="<?php echo $value->id; ?>" id="click_alert_btn" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("ACCOUNT_ASSIGN_PASS")?>"></i>
                                                                    <input type="hidden" name="submit" value="submit"></input>
                                                                </form>
                                                            <?php
                                                            }
                                                            else
                                                            {
                                                                ?>
                                                                    <i class="zmdi zmdi-hc-fw zmdi-lock m-r-xs" style="opacity: 0; cursor: default !important;"></i>
                                                                    <input type="hidden" name="submit" value="submit"></input>
                                                                <?php

                                                            }

                                                            if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                                                <i class="zmdi zmdi-hc-lg zmdi-edit edit_account m-r-xs change_page" style="display: inline-block;" page="edit_account" args="&user_id=<?php echo $value->id; ?>" id="edit_account" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT_ACCOUNT")?>"></i>
                                                            <?php
                                                            }


                                                            if (RightsHandler::has_user_right("ACCOUNT_DELETE")) { ?>
                                                            <form style="display: inline-block;" method="post" id="click_alert_form_<?php echo $value->id; ?>" url="edit_account.php?step=delete_acc">
                                                                <input type="hidden" name="user_id" value="<?php echo $value->id; ?>">
                                                                <i class="zmdi zmdi-hc-lg zmdi-delete btn_click_alertbox" current_datatable="datatable_1" element_id="<?php echo $value->id; ?>" id="click_alert_btn" style="" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("DELETE")?>"></i>
                                                                <input type="hidden" name="submit" value="submit"></input>
                                                            </form>
                                                            <?php } ?>
                                                        </div>
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
                    <div class="my_fade my_tab" id="assign_pass_tab">
                        <div class="widget-body">
                            <form method="post" id="assign_password_form" url="edit_account.php?step=assign_passwords">
                            <table id="default-datatable" class="table dataTable" style="margin:20px 0px 25px 0px !important;" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false, targets: [0]}],order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid"
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") || RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("PICK"); ?></th>
                                        <?php } ?>
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <th hidden></th>
                                    </tr>

                                </thead>

                                    <tfoot>
                                        <tr role="row">
                                            <th></th>
                                            <th>
                                                <div class="form-group" style="margin-left:-100px;">
                                                    <div class="col-md-12">
                                                        <?php if (RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") || RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD")) { ?>
                                                            <input type="button" name="submit" id="assign_password_submit" value="<?php echo TranslationHandler::get_static_text("ACCOUNT_ASSIGN_PASS"); ?>" class="btn btn-default account_assign_password">
                                                        <?php } ?>

                                                    </div>
                                                </div>
                                            </th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th hidden></th>
                                        </tr>



                                    </tfoot>

                                    <tbody>
                                        <?php
                                        if (RightsHandler::has_user_right("ACCOUNT_FIND")) {
                                            $userHandler->get_all_users_without_password();
                                            foreach ($userHandler->users as $value) {

                                                ?>
                                                <tr class="clickable_row" >
                                                    <?php if (RightsHandler::has_user_right("ACCOUNT_ASSIGN_PASSWORD") || RightsHandler::has_user_right("ACCOUNT_ASSIGN_STUDENT_PASSWORD")) { ?>
                                                        <td style="text-align: center">
                                                                <div class="checkbox">
                                                                    <input class="checkbox-circle checkbox-dark" type="checkbox" value="<?php echo $value->id; ?>" name="user_ids[]">
                                                                    <label></label>
                                                                </div>
                                                        </td>
                                                    <?php } ?>

                                                    <td class="click_select_me" data-search="<?php echo $value->firstname . " " . $value->surname; ?>"><?php echo (strlen($value->firstname . " " . $value->surname) > 20 ? substr($value->firstname . " " . $value->surname, 0, 20) : $value->firstname . " " . $value->surname); ?></td>
                                                    <td class="click_select_me"><?php echo $value->username; ?></td>
                                                    <td class="click_select_me"><?php echo $value->user_type_title; ?></td>
                                                    <td class="click_select_me" data-search="<?php echo $value->email ?>"><?php echo (strlen($value->email) > 20 ? substr($value->email, 0, 20) : $value->email); ?></td>
                                                    <td class="click_select_me" data-search="<?php echo $value->school_name ?>"><?php echo (strlen($value->school_name) > 16 ? substr($value->school_name, 0, 16) : $value->school_name); ?></td>

                                                    <td class="hidden"><?php echo $value->id; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </tbody>

                            </table>
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

<div style="display:none;" id="open_text"><?php echo TranslationHandler::get_static_text("CONFIRM_CLOSE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("ACCOUNT")) . "?"; ?></div>
<div style="display:none;" id="close_text"><?php echo TranslationHandler::get_static_text("CONFIRM_OPEN") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("ACCOUNT")) . "?"; ?></div>

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

<div id="click_alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div id="delete_text"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE") . " " . strtolower(TranslationHandler::get_static_text("THIS")) . " " . strtolower(TranslationHandler::get_static_text("ACCOUNT")) . "?"; ?></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>

<div id="click_alertbox_exp" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
        <div class="" id="pass_assign_confirm_text"><?php echo TranslationHandler::get_static_text("CONFIRM_ASSIGN_PASS"); ?></div>
        <div class="hidden" id="pass_assigned_text"><span><?php echo TranslationHandler::get_static_text("ASSIGNED_PASSWORD") . " "; ?></span><span id="pass_assigned_text_span"></span></div>
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0" id="pass_assign_confirm">
            <input class="btn btn-default btn-sm p-v-lg accept_click_alertbox_exp_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_exp_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
        <p class="m-0 hidden" id="pass_assign_close">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_exp_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
        </p>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script>$(document).ready(function(){$("[data-toggle='tooltip']").tooltip()});</script>
