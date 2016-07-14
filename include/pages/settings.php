<?php
require_once '../../include/ajax/require.php';
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
                            <li role="presentation" class="active"><a href="#tab-1" aria-controls="tab-3" role="tab" data-toggle="tab" aria-expanded="true"><?php echo TranslationHandler::get_static_text("INFO_EDIT_PROFILE"); ?></a></li>
                            <li role="presentation" class=""><a href="#tab-2" aria-controls="tab-1" role="tab" data-toggle="tab" aria-expanded="false"><?php echo TranslationHandler::get_static_text("INFO_CHANGE_PASS"); ?></a></li>
                            <li role="presentation" class=""><a href="#tab-3" aria-controls="tab-1" role="tab" data-toggle="tab" aria-expanded="false"><?php echo TranslationHandler::get_static_text("INFO_PREFERENCES"); ?></a></li>
			</ul><!-- .nav-tabs -->

						<!-- Tab panes -->
						<div class="tab-content p-md">
							<div role="tabpanel" class="tab-pane fade active in" id="tab-1">
								<div class="widget-body">
                                                                        <form method="post" action="" url="settings.php?step=1" id="edit_info">
                                                                        <div class="col-md-6">

                                                                            <div class="form-group m-b-sm">
                                                                                <label for="firstname_input"><?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?></label>
                                                                                <input type="text" id="firstname_input" name="firstname" value="<?php echo $userHandler->_user->firstname; ?>" class="form-control">
                                                                            </div>

                                                                            <div class="form-group m-b-sm">
                                                                                <label for="surname_input"><?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?></label>
                                                                                <input type="text" id="surname_input" name="surname" value="<?php echo $userHandler->_user->surname; ?>" class="form-control">
                                                                            </div>

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
                                                                                                                <a href="javascript:void(0)"><img src="assets/images/profile_images/<?php echo $userHandler->_user->image_id;?>.png" alt=""></a>
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="media-body">
                                                                                                        <h5 class="media-heading"><a href="javascript:void(0)" class="title-color"><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname;?></a></h5>
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
                                                                                                                    echo '<div class="avatar avatar-xl avatar-circle avatar-hover"><img src="assets/images/profile_images/' . $image['id'] . '.png"/></div>';
                                                                                                                }
                                                                                                            ?>
                                                                                                    </div>
                                                                                            </div>
                                                                                    </div>
                                                                                </div>


                                                                            </div>
                                                                        </div>

                                        <div style="clear:both;"></div>

                                        <div class="form-group">
                                            <div class="col-md-12">

                                                <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-left btn btn-default btn-sm create_submit_info">
                                            </div>
                                        </div>
                                    </form>
                                </div>
			</div><!-- .tab-pane  -->


			<div role="tabpanel" class="tab-pane fade" id="tab-2">

                            <div class="widget-body">
                                <form method="POST" action="" id="settings_pass" url="settings.php?step=2" name="settings_pass">
                                                                        <div class="">

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
                                                                        </div>




                                    </form>
                                </div>


                        </div><!-- .tab-pane  -->





                        <div role="tabpanel" class="tab-pane fade" id="tab-3">



                        </div><!-- .tab-pane  -->


                    </div><!-- .tab-content  -->
		</div><!-- .nav-tabs-horizontal -->
            </div><!-- .widget -->
	</div>
    </div>