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
                            <table id="default-datatable" class="table dataTable" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>, lengthMenu:[5, 10, 25, 50, 100], columnDefs:[{orderable: false, targets: [5,6]}]}" data-plugin="DataTable" role="grid" 
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
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
                                            <th><?php echo TranslationHandler::get_static_text("OPEN"); ?></th>
                                        <?php } ?>
                                        <?php if (RightsHandler::has_user_right("ACCOUNT_EDIT")) { ?>
                                            <th><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden></th>
                                    </tr>
                                </tfoot>
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
                            
                        </div>
                    </div>
                    <!-- End Tab -->

                    <!-- Tab -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
