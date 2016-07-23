<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';

$userHandler = new UserHandler();

//REDIGER BRUGER INFO
?>


<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="edit_info_header"><a href="#edit_user_info_tab" class="my_tab_header" id="edit_user_info_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_EDIT_PROFILE"); ?></a></li>

                    <?php
                    if (RightsHandler::has_user_right("CHANGE_PASSWORD")) {
                        ?>
                        <li role="presentation" id="change_password_header"><a href="#change_password_tab" class="my_tab_header" id="change_password_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_CHANGE_PASS"); ?></a></li>
                        <?php
                    }
                    ?>

                    <?php
                    if (RightsHandler::has_page_right("SETTINGS_PREFERENCES")) {
                        ?>
                        <li role="presentation" id="preferences_header"><a href="#preferences_tab" class="my_tab_header" id="preferences_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_PREFERENCES"); ?></a></li>
                        <?php
                    }
                    ?>
                </ul><!-- .nav-tabs -->

                <!-- Tab panes -->
                <div class="my_tab_content">
                    <div class="my_fade my_tab" id="edit_user_info_tab">
                        <div class="widget-body">
                            <form method="post" action="" url="settings.php?step=edit_info" id="edit_info_form" name="edit_info">
                                <input name="avatar_hidden_id" id="avatar_hidden_id" class="input_avatar_id" type="hidden" value="<?php echo $userHandler->_user->image_id; ?>" />
                                <div class="col-md-6">
<?php
if (RightsHandler::has_user_right("CHANGE_FULL_NAME")) {
    ?>
                                        <div class="form-group m-b-sm">
                                            <label for="firstname_input"><?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?></label>
                                            <input type="text" id="firstname_input" name="firstname" value="<?php echo $userHandler->_user->firstname; ?>" class="form-control input_change input_firstname">
                                        </div>

                                        <div class="form-group m-b-sm">
                                            <label for="surname_input"><?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?></label>
                                            <input type="text" id="surname_input" name="surname" value="<?php echo $userHandler->_user->surname; ?>" class="form-control input_change input_surname">
                                        </div>
    <?php
}
?>
                                    <div class="form-group m-b-sm">
                                        <label for="email_input"><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></label>
                                        <input type="text" id="email_input" name="email" value="<?php echo $userHandler->_user->email; ?>" class="form-control">
                                    </div>

                                    <div class="form-group m-b-sm">
                                        <label for="textarea1"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></label>

                                        <textarea class="form-control" id="textarea1" name="description"><?php echo $userHandler->_user->description; ?></textarea>

                                    </div>
                                </div>






                                <div class="col-md-6">
                                    <div class="form-group m-b-sm">

                                        <div class="user-card p-md" style="margin-top: 25px;">
                                            <div class="media">
                                                <div class="media-left">
                                                    <div class="avatar avatar-lg avatar-circle">
                                                        <img class="current-avatar" src="assets/images/profile_images/<?php echo $userHandler->_user->image_id; ?>.png" alt="">
                                                    </div>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading"><a href="javascript:void(0)" class="title-color user_full_name"><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname; ?></a></h5>
                                                    <small class="media-meta"><?php echo $userHandler->_user->user_type_title; ?></small>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="panel-group accordion" id="accordion" role="tablist" aria-multiselectable="false">
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="heading-1">
                                                    <a class="accordion-toggle" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="false" aria-controls="collapse-1">
                                                        <label for="textarea1"><?php echo TranslationHandler::get_static_text("INFO_CHANGE_IMAGE"); ?></label>
                                                        <i class="fa acc-switch"></i>
                                                    </a>
                                                </div>
                                                <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1" aria-expanded="false">
                                                    <div class="panel-body">
<?php
$userHandler->get_profile_images();
foreach ($userHandler->profile_images as $image) {
    echo '<div class="avatar avatar-xl avatar-circle avatar-hover" avatar_id="' . $image['id'] . '"><img src="assets/images/profile_images/' . $image['id'] . '.png"/></div>';
}
?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default btn-sm create_submit_info">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- .tab-pane  -->

<?php
if (RightsHandler::has_user_right("CHANGE_PASSWORD")) {
    ?>
                        <div class="my_fade my_tab" id="change_password_tab">

                            <div class="widget-body">
                                <form method="POST" action="" id="settings_pass" url="settings.php?step=change_password" name="settings_pass">
                                    <div class="col-md-12">
                                    <div class="form-group m-b-sm">
                                        <label for="firstname_input"><?php echo TranslationHandler::get_static_text("OLD_PASSWORD"); ?></label>
                                        <input type="password" id="old_password" name="old_password" placeholder="<?php echo TranslationHandler::get_static_text("OLD_PASSWORD"); ?>" class="form-control">
                                    </div>

                                    <div class="form-group m-b-sm">
                                        <label for="surname_input"><?php echo TranslationHandler::get_static_text("PASSWORD"); ?></label>
                                        <input type="password" id="password" name="password" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>" class="form-control">
                                    </div>

                                    <div class="form-group m-b-sm">
                                        <label for="email_input"><?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?></label>
                                        <input type="password" id="confirm_password" name="confirm_password" placeholder="<?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?>" class="form-control">
                                    </div>

                                    <div class="form-group m-b-sm pull-right">
                                        <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="btn btn-default btn-sm create_submit_info" >
                                    </div>
                                    </div>
                                </form>
                            </div>


                        </div><!-- .tab-pane  -->
<?php
}
if (RightsHandler::has_page_right("SETTINGS_PREFERENCES")) {
?>
                     <div class="my_fade my_tab" id="preferences_tab">
                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="settings.php?step=preferences" id="preferences" name="preferences">
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("GENERAL_PREFERENCES"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-sm">
                                            <label for="language" class="control-label"><?php echo TranslationHandler::get_static_text("LANGUAGE"); ?>:</label>
                                            <select id="language" name="language" class="form-control">
                                                <?php
                                                foreach(TranslationHandler::get_language_options() as $language) {
                                                    echo '<option value="'.$language["id"].'" '. ($language["id"] == TranslationHandler::get_current_language() ? 'selected' : '') .'>'.$language["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="os" class="control-label"><?php echo TranslationHandler::get_static_text("OS"); ?>:</label>
                                            <select id="os" name="os" class="form-control">
                                                <?php
                                                $course_os_data = DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title FROM course_os INNER JOIN translation_course_os ON translation_course_os.course_os_id = course_os.id WHERE translation_course_os.language_id = :language_id", TranslationHandler::get_current_language());
                                                foreach($course_os_data as $os) {
                                                    echo '<option value="'.$os["id"].'">'.$os["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="os" class="control-label"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_ELEMENTS_SHOWN"); ?>:</label>
                                            <select id="os" name="os" class="form-control">
                                                <option value="5">5</option>
                                                <option value="5">10</option>
                                                <option value="5">25</option>
                                                <option value="5">50</option>
                                            </select>
                                        </div>
                                        
                                        <?php
                                        if(RightsHandler::has_user_right("ACCOUNT_HIDE_PROFILE")) {
                                        ?>
                                        
                                        <div class="form-group m-b-sm" style="margin-top:20px !important;">
                                            <div class="checkbox" style="float:left;">
                                                <input name="hide_profile" class="form-control" type="checkbox" id="checkbox-hide-profile"> <label for="checkbox-hide-profile"></label>
                                            </div>
                                            <div><?php echo TranslationHandler::get_static_text("HIDE_PROFILE_FROM_STUDENTS"); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MAIL_PREFERENCES"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        <?php
                                        if(RightsHandler::has_user_right("NOTIFICATION_BLOCK_MAILS")) {
                                        ?>
                                        <div class="form-group m-b-sm" style="">
                                            <div class="checkbox" style="float:left;">
                                                <input name="block_mail_notifications" class="form-control" type="checkbox" id="checkbox-block-notifications"> <label for="checkbox-block-notifications"></label>
                                            </div>
                                            <div><?php echo TranslationHandler::get_static_text("BLOCK_MAIL_NOTIFICATIONS"); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <?php
                                        }
                                        
                                        if(RightsHandler::has_user_right("MAIL_BLOCK_STUDENTS")) {
                                        ?>
                                        
                                        <div class="form-group m-b-sm" style="margin-top:-10px !important;">
                                            <div class="checkbox" style="float:left;">
                                                <input name="block_student_mails" class="form-control" type="checkbox" id="checkbox-block-student-mails"> <label for="checkbox-block-student-mails"></label>
                                            </div>
                                            <div><?php echo TranslationHandler::get_static_text("BLOCK_MAILS_FROM_STUDENTS"); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    
                                    <?php
                                    if(RightsHandler::has_user_right("MAIL_BLOCK_STUDENTS")) {
                                    ?>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("BLOCK_STUDENTS"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                    </div>
                                    
                                    <?php
                                    }
                                    ?>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default btn-sm create_submit_info">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
<?php
}
?>

                </div><!-- .tab-content  -->
            </div><!-- .nav-tabs-horizontal -->
        </div><!-- .widget -->
    </div>
</div>

<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>