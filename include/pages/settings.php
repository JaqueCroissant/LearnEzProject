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
                            <li role="presentation" id="edit_info_header"><a href="#edit_info" class="my_tab_header" id="edit_info_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_EDIT_PROFILE"); ?></a></li>

                            <?php
                                if(RightsHandler::has_user_right("CHANGE_PASSWORD"))
                                {
                            ?>
                                    <li role="presentation" id="change_password_header"><a href="#change_password" class="my_tab_header" id="change_password_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_CHANGE_PASS"); ?></a></li>
                            <?php
                                }
                            ?>

                            <?php
                                if(RightsHandler::has_page_right("SETTINGS_PREFERENCES"))
                                {
                            ?>
                                    <li role="presentation" id="preferences_header"><a href="#preferences" class="my_tab_header" id="preferences_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("INFO_PREFERENCES"); ?></a></li>
                            <?php
                                }
                            ?>
                        </ul><!-- .nav-tabs -->

						<!-- Tab panes -->
						<div class="my_tab_content">
							<div class="my_fade my_tab" id="edit_info">
								<div class="widget-body">
                                                                        <form method="post" action="" url="settings.php?step=edit_info" id="edit_info_form" name="edit_info">
                                                                            <input name="avatar_hidden_id" id="avatar_hidden_id" class="input_avatar_id" type="hidden" value="<?php echo $userHandler->_user->image_id;?>" />
                                                                        <div class="col-md-6">
                                                                            <?php
                                                                                if(RightsHandler::has_user_right("CHANGE_FULL_NAME"))
                                                                                {
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
                                                                                                                <img class="current-avatar" src="assets/images/profile_images/<?php echo $userHandler->_user->image_id;?>.png" alt="">
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="media-body">
                                                                                                        <h5 class="media-heading"><a href="javascript:void(0)" class="title-color user_full_name"><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname;?></a></h5>
                                                                                                        <small class="media-meta"><?php echo $userHandler->_user->user_type_title;?></small>
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
                                                                                                                foreach($userHandler->profile_images as $image)
                                                                                                                {
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

                                                <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-left btn btn-default btn-sm submit_edit_user_info">
                                            </div>
                                        </div>
                                    </form>
                                </div>
			</div><!-- .tab-pane  -->

                        <?php
                            if(RightsHandler::has_user_right("CHANGE_PASSWORD"))
                            {
                        ?>
                            <div class="my_fade my_tab" id="change_password">

                                <div class="widget-body">
                                     <form method="POST" action="" id="settings_pass" url="settings.php?step=change_password" name="settings_pass">


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

                                                                                <div class="form-group m-b-sm">
                                                                                    <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="btn btn-default btn-sm create_submit_info" >
                                                                                </div>





                                        </form>
                                </div>


                            </div><!-- .tab-pane  -->
                        <?php
                            }
                        ?>




                        <div class="my_fade my_tab" id="preferences">



                        </div><!-- .tab-pane  -->


                    </div><!-- .tab-content  -->
		</div><!-- .nav-tabs-horizontal -->
            </div><!-- .widget -->
	</div>
    </div>

<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>