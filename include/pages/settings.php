<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/mailHandler.php';
require_once '../../include/handler/mediaHandler.php';

$userHandler = new UserHandler();
$mailHandler = new MailHandler();
$mediaHandler = new MediaHandler();
?>


<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    if (RightsHandler::has_page_right("SETTINGS_EDIT_USER_INFO")) {
                        ?>
                    <li role="presentation" id="edit_info_header"><a href="#edit_user_info_tab" class="my_tab_header" id="edit_user_info_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_EDIT_PROFILE"); ?></a></li>
                    <?php
                    }
                    
                    if (RightsHandler::has_user_right("CHANGE_PASSWORD")) {
                    ?>
                        <li role="presentation" id="change_password_header"><a href="#change_password_tab" class="my_tab_header" id="change_password_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_CHANGE_PASS"); ?></a></li>
                        <?php
                    }
                    
                    if (RightsHandler::has_page_right("SETTINGS_PREFERENCES")) {
                        ?>
                        <li role="presentation" id="preferences_header"><a href="#preferences_tab" class="my_tab_header" id="preferences_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_PREFERENCES"); ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
                
                
                <div class="my_tab_content">
                    <?php
                    if (RightsHandler::has_page_right("SETTINGS_EDIT_USER_INFO")) {
                    ?>
                    <div class="my_fade my_tab" id="edit_user_info_tab">
                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="settings.php?step=edit_info" id="edit_info_form" name="edit_info">
                                <input type="hidden" name="avatar_hidden_id" id="avatar_hidden_id" class="input_avatar_id" value="<?php echo $userHandler->_user->image_id; ?>" />
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PERSONAL_INFORMATION"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
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
                                </div>

                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PROFILE"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                    <div class="form-group m-b-sm">

                                        <div class="user-card p-md" style="margin-top: 25px;">
                                            <div class="media">
                                                <div class="media-left">
                                                    <div class="avatar avatar-lg avatar-circle">
                                                        <img class="current-avatar" src="assets/images/profile_images/<?php echo profile_image_exists($userHandler->_user->profile_image); ?>" alt="">
                                                    </div>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading"><a href="javascript:void(0)" class="title-color user_full_name"><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname ?></a></h5>
                                                    <small class="media-meta"><?php echo $userHandler->_user->user_type_title; ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PROFILE_IMAGE"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-profile-image" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-profile-image">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-profile-image" href="#collapse-profile-image" aria-expanded="false" aria-controls="collapse-profile-image">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("PICK_FROM_EXISTING_PROFILE_IMAGE"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-profile-image" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-profile-image" aria-expanded="false">
                                                        <div class="panel-body profile-images-placeholder" style="padding-left:0px !important;padding-right:0px !important;">
                                                            <?php
                                                            if($mediaHandler->get_profile_images()) {
                                                                foreach ($mediaHandler->profile_images as $value) {
                                                                    echo '<div class="avatar avatar-xl profile_image_element" profile_image_id="' . $value['id'] . '" style="cursor:pointer;z-index:10;'. ($userHandler->_user->image_id > 0 && $userHandler->_user->image_id == $value['id'] ? '' : ($userHandler->_user->image_id > 0 ? 'opacity: 0.5' : '')) .'"><div class="delete_profile_image delete_profile_image_style hidden" title="'.TranslationHandler::get_static_text("DELETE_PROFILE_IMAGE").'" profile_image_id="' . $value['id'] . '"><i class="zmdi zmdi-close" style="display:initial !important;"></i></div><img style="border-radius: 100% !important;" src="assets/images/profile_images/' . $value['filename'] . '"/><div class="active_profile_image '. ($userHandler->_user->image_id > 0 && $userHandler->_user->image_id == $value['id'] ? '' : 'hidden') .'" title="'.TranslationHandler::get_static_text("PICK_PROFILE_IMAGE").'" profile_image_id="' . $value['id'] . '"><i class="zmdi zmdi-check" style="display:initial !important;"></i></div></div>';
                                                                }
                                                            } else {
                                                                echo "<div style='width:100%;text-align:center;'>".$mediaHandler->error->title."</div>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-new-profile-image" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-new-profile-image">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-new-profile-image" href="#collapse-new-profile-image" aria-expanded="false" aria-controls="collapse-new-profile-image">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("UPLOAD_NEW_PROFILE_IMAGE"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-new-profile-image" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-new-profile-image" aria-expanded="false">
                                                    <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                                    <div class="form-group m-b-sm">
                                                        <div style="display: table-cell;width: 100%;">
                                                        <input type="file" id="profile_image_upload" name="profile_image_upload" class="form-control btn btn-default">
                                                        </div>
                                                        <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                                        <input type="button" name="submit" id="upload_profile_image" value="<?php echo TranslationHandler::get_static_text("UPLOAD_IMAGE"); ?>" class="pull-right btn btn-default btn-md upload_profile_image m-l-sm">
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default btn-sm submit_edit_user_info">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    </div>
                    <?php
                    }
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


                    </div>
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
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        <div class="form-group m-b-sm">
                                            <label for="language" class="control-label"><?php echo TranslationHandler::get_static_text("LANGUAGE"); ?>:</label>
                                            <select id="language" name="language" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                foreach(TranslationHandler::get_language_options() as $language) {
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.$language["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="os" class="control-label"><?php echo TranslationHandler::get_static_text("OS"); ?>:</label>
                                            <select id="os" name="os" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                $course_os_data = DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title FROM course_os INNER JOIN translation_course_os ON translation_course_os.course_os_id = course_os.id WHERE translation_course_os.language_id = :language_id", TranslationHandler::get_current_language());
                                                foreach($course_os_data as $os) {
                                                    echo '<option value="'.$os["id"].'"'. (SettingsHandler::get_settings()->os_id == $os["id"] ? 'selected' : '') .'>'.$os["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="elements_shown" class="control-label"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_ELEMENTS_SHOWN"); ?>:</label>
                                            <select id="elements_shown" name="elements_shown" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option value="5" <?php echo SettingsHandler::get_settings()->elements_shown == "5" ? "selected" : ""; ?>>5</option>
                                                <option value="10" <?php echo SettingsHandler::get_settings()->elements_shown == "10" ? "selected" : ""; ?>>10</option>
                                                <option value="25" <?php echo SettingsHandler::get_settings()->elements_shown == "25" ? "selected" : ""; ?>>25</option>
                                                <option value="50" <?php echo SettingsHandler::get_settings()->elements_shown == "50" ? "selected" : ""; ?>>50</option>
                                                <option value="100" <?php echo SettingsHandler::get_settings()->elements_shown == "100" ? "selected" : ""; ?>>100</option>
                                            </select>
                                        </div>
                                        
                                        <?php
                                        if(RightsHandler::has_user_right("ACCOUNT_HIDE_PROFILE")) {
                                        ?>
                                        
                                        <div class="form-group m-b-sm" style="margin-top:20px !important;">
                                            <div class="checkbox" style="float:left;">
                                                <input name="hide_profile" class="form-control" type="checkbox" id="checkbox-hide-profile" <?php echo SettingsHandler::get_settings()->hide_profile ? "checked" : ""; ?>> <label for="checkbox-hide-profile"></label>
                                            </div>
                                            <div><?php echo TranslationHandler::get_static_text("HIDE_PROFILE_FROM_STUDENTS"); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MAIL_PREFERENCES"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        <?php
                                        if(RightsHandler::has_user_right("NOTIFICATION_BLOCK_MAILS")) {
                                        ?>
                                        <div class="form-group m-b-sm" style="">
                                            <div class="checkbox" style="float:left;">
                                                <input name="block_mail_notifications" class="form-control" type="checkbox" id="checkbox-block-notifications" <?php echo SettingsHandler::get_settings()->block_mail_notifications ? "checked" : ""; ?>> <label for="checkbox-block-notifications"></label>
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
                                                <input name="block_student_mails" class="form-control" type="checkbox" id="checkbox-block-student-mails" <?php echo SettingsHandler::get_settings()->block_student_mails ? "checked" : ""; ?>> <label for="checkbox-block-student-mails"></label>
                                            </div>
                                            <div><?php echo TranslationHandler::get_static_text("BLOCK_MAILS_FROM_STUDENTS"); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <?php
                                    if(RightsHandler::has_user_right("MAIL_BLOCK_STUDENTS")) {
                                    ?>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("BLOCK_STUDENTS"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-lg">
                                            <div style="display: table-cell;width: 100%;">
                                            <label for="student" class="control-label"><?php echo TranslationHandler::get_static_text("PICK_STUDENT"); ?>:</label>
                                            <select id="student" name="student" class="form-control pull-left" data-plugin="select2">
                                                <?php
                                                foreach($mailHandler->get_students() as $value) {
                                                    echo '<option value="'.$value->id.'">'. format_first_last_name($value->firstname, $value->surname, 40) .'</option>';
                                                }
                                                ?>
                                            </select>
                                            </div>
                                            <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                            <input type="button" name="submit" id="block_student" value="<?php echo TranslationHandler::get_static_text("BLOCK"); ?>" class="pull-right btn btn-default btn-md block_student m-l-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group m-b-sm blocked_students">
                                            <label class="control-label"><?php echo TranslationHandler::get_static_text("BLOCKED_STUDENTS"); ?>:</label>
                                            <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                            <?php
                                            if(!empty(SettingsHandler::get_settings()->blocked_students)) {
                                                ?>
                                                <div class="center no_students_text" style="display:none;"><?php echo TranslationHandler::get_static_text("NO_BLOCKED_STUDENTS"); ?></div>
                                                <?php
                                                foreach(SettingsHandler::get_settings()->blocked_students as $user) {
                                                    ?>
                                                    <div class="div_student_<?php echo $user->id; ?> blocked_student">
                                                        <input type="hidden" name="blocked_student[]" value="<?php echo $user->id; ?>"/>
                                                        <div class="user-card m-b-sm student_<?php echo $user->id; ?>" style="padding: 8px !important;background:#f0f0f1;">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <?php echo format_first_last_name($user->firstname, $user->surname, 40) ?>
                                                                    <i class="zmdi zmdi-hc-lg zmdi-close pull-right remove_blocked_student" student_id="<?php echo $user->id; ?>" style="margin-top:4px;cursor:pointer;"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                                <div class="center no_students_text"><?php echo TranslationHandler::get_static_text("NO_BLOCKED_STUDENTS"); ?></div>
                                            <?php
                                            }
                                            ?>
                                        </div>
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

                </div>
            </div>
        </div>
    </div>
</div>

<!--<script src="assets/js/include_library.js" type="text/javascript"></script>-->
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>