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
                    <li role="presentation" id="find_account_header"><a href="#find_account_tab" class="my_tab_header" id="find_account_a" data-toggle="tab">
                            <?php echo TranslationHandler::get_static_text("FIND_ACCOUNT"); ?></a></li>
                    <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                        <li role="presentation" id="edit_account_header" class="hidden"><a href="#edit_account_tab" class="my_tab_header" id="edit_account_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("EDIT_ACCOUNT"); ?></a></li>
                    <?php } ?>
                </ul><!-- .nav-tabs -->

                <div class="my_tab_content">
                    <!-- Tab -->
                    <div class="my_fade my_tab" id="find_account_tab">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable" style="margin:20px 0px 25px 0px !important;" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false, targets: [5,6]}], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid"
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
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
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden></th>
                                    </tr>
                                </tfoot>

                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("ACCOUNT_FIND")) {

                                        foreach ($userHandler->users as $value) {

                                            ?>
                                            <tr class="clickable_row">

                                                <td class="click_me" data-search="<?php echo $value->firstname . " " . $value->surname; ?>"><?php echo (strlen($value->firstname . " " . $value->surname) > 20 ? substr($value->firstname . " " . $value->surname, 0, 20) : $value->firstname . " " . $value->surname); ?></td>
                                                <td class="click_me"><?php echo $value->username; ?></td>
                                                <td class="click_me"><?php echo $value->user_type_title; ?></td>
                                                <td class="click_me" data-search="<?php echo $value->email ?>"><?php echo (strlen($value->email) > 20 ? substr($value->email, 0, 20) : $value->email); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->school_name ?>"><?php echo (strlen($value->school_name) > 16 ? substr($value->school_name, 0, 16) : $value->school_name); ?></td>
                                                <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
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
                                                <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                                    <td align="center">
                                                        <div class="">
                                                            <i class="zmdi zmdi-hc-lg zmdi-edit edit_account m-r-xs change_page" page="edit_account" args="&user_id=<?php echo $value->id; ?>" id="edit_account"></i>
                                                            <?php if (RightsHandler::has_user_right("ACCOUNT_DELETE")) { ?>
                                                                <i class="zmdi zmdi-hc-lg zmdi-delete delete_account" account_id="<?php echo $value->id; ?>" state="delete_account" id="delete_account_btn" style=""></i>
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
                    <div class="my_fade my_tab" id="edit_account_tab">
                        <div class="widget-body">
                            
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

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
