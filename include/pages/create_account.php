<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/schoolHandler.php';
    require_once '../../include/handler/classHandler.php';
    
    $userHandler = new UserHandler();
    $schoolHandler = new SchoolHandler();
    $classHandler = new ClassHandler();

    ?>
        <!--GRUNDLÆGGENDE INFO + TILHØRSFORHOLD FOR OPRETTELSE AF ENKELT BRUGER-->

        <div class="row">   
            <div class="col-md-12">
				<div class="widget">
					<div class="m-b-lg nav-tabs-horizontal">
						<!-- tabs list -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#tab-1" aria-controls="tab-3" role="tab" data-toggle="tab" aria-expanded="true"><?php echo TranslationHandler::get_static_text("CREATE_NEW_PROFILE"); ?></a></li>
							<li role="presentation" class=""><a href="#tab-2" aria-controls="tab-1" role="tab" data-toggle="tab" aria-expanded="false"><?php echo TranslationHandler::get_static_text("CREATE_IMPORT_PROFILES"); ?></a></li>
						</ul><!-- .nav-tabs -->

						<!-- Tab panes -->
						<div class="tab-content p-md">
							<div role="tabpanel" class="tab-pane fade active in" id="tab-1">
								<div class="widget-body">

                                    <form method="POST" action="" id="create_single" url="create_account.php?step=1" name="create_single_user">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CREATE_BASIC_INFO"); ?></h4></br>
                                                <input type="text" id="firstname" name="firstname" placeholder="<?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?>" class="form-control"><br/>
                                                <input type="text" id="surname" name="surname" placeholder="<?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?>" class="form-control"><br/>
                                                <input type="text" id="email" name="email" placeholder="<?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>" class="form-control"><br/>
                                                <input type="password" id="password" name="password" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>" class="form-control"><br/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CREATE_AFFILIATIONS"); ?></h4></br>
                                                <select id="user_type" name ="usertype" class="create_select_usertype form-control">
                                                    <?php
                                                        if($userHandler->_user->user_type_id==1)
                                                        {
                                                            echo '<option value="SA">' . TranslationHandler::get_static_text("SUPER_ADMIN") . '</option>';
                                                            echo '<option value="A">' . TranslationHandler::get_static_text("ADMIN") . '</option>';
                                                        }
                                                    ?>
                                                    <option value="T"><?php echo TranslationHandler::get_static_text("TEACHER"); ?></option>'
                                                    <option value="S"><?php echo TranslationHandler::get_static_text("STUDENT"); ?></option>'
                                                </select>
                                                </br>

                                                <?php
                                                if($userHandler->_user->user_type_id==1)
                                                {?>

                                                    <select id="select1" name="school_id" class="create_select_school form-control" data-plugin="select2">
                                                        <option value="default"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_SCHOOL"); ?></option>
                                                        <?php
                                                            $schoolHandler->get_all_schools();
                                                            foreach($schoolHandler->all_schools as $school)
                                                            {
                                                                echo '<option value = "' . $school->id . '">' . $school->name . ', ' . $school->address . '</option>';
                                                            }
                                                        ?>

                                                    </select>
                                                    </br>
                                                <?php }

                                                    if($userHandler->_user->user_type_id == 1)
                                                    {   ?>
                                                        <select id="select1" name="class_name" class="create_select_class form-control hidden" data-plugin="select2"></select>
                                                    <?php }
                                                    else
                                                    {?>

                                                        <select id="select1" name="class_name" class="create_select_class form-control" data-plugin="select2">
                                                            <option value=""><?php echo TranslationHandler::get_static_text("CREATE_SELECT_CLASS"); ?></option>
                                                            <?php
                                                                $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
                                                                foreach($classHandler->classes_in_school as $class)
                                                                {
                                                                    echo '<option value = "' . $class->id . '">' . $class->title . '</option>';
                                                                }
                                                            ?>

                                                        </select>
                                                        </br>
                                                    <?php

                                                    }
                                                ?>


                                                    </br>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>

                                        <div class="form-group">
                                            <div class="col-md-12">

                                                <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("CREATE_SUBMIT"); ?>" class="pull-right btn btn-default btn-sm create_submit_info">
                                            </div>
                                        </div>
                                    </form>
                                </div style="clear:both;">
							</div><!-- .tab-pane  -->



							<div role="tabpanel" class="tab-pane fade" id="tab-2">
                                <div class="widget-body">
                                    <form method="POST" action="" id="create_import" url="createprofile.php" name="create_import" class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-3">
                                                <?php
                                                if($userHandler->_user->user_type_id==1)
                                                {?>

                                                    <select id="select1" name="school_id" class="form-control" data-plugin="select2">
                                                        <option value=""><?php echo TranslationHandler::get_static_text("CREATE_SELECT_SCHOOL"); ?></option>
                                                        <?php
                                                            $schoolHandler->get_all_schools();
                                                            foreach($schoolHandler->all_schools as $school)
                                                            {
                                                                echo '<option value = "' . $school->id . '">' . $school->name . ', ' . $school->address . '</option>';
                                                            }
                                                        ?>

                                                    </select>
                                                    </br>
                                                <?php }
                                                ?>

                                                <div class="form-group">
                                                    <input type="file" id="csv_file_dialog" name="csv_file" accept=".csv" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_NAME"); ?>" class="btn btn-default btn-sm"><br/>
                                                </div>
                                            </div>

                                            <div style="clear:both;"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-md-offset-3">
                                                    <input type="button" id="submit" name="submit" value="<?php echo TranslationHandler::get_static_text("CREATE_IMPORT"); ?>" class="btn btn-default btn-sm create_import_profiles">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div >
                            </div><!-- .tab-pane  -->
						</div><!-- .tab-content  -->
					</div><!-- .nav-tabs-horizontal -->
				</div><!-- .widget -->
			</div>
        </div>