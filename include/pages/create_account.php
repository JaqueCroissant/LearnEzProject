<?php
    require_once 'require.php';
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
							<li role="presentation" id="create_user_header"><a href="#create_user" class="my_tab_header" id="create_user_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_NEW_PROFILE"); ?></a></li>
							<li role="presentation" id="import_users_header"><a href="#import_users" class="my_tab_header" id="import_users_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_IMPORT_PROFILES"); ?></a></li>
						</ul><!-- .nav-tabs -->

						<!-- Tab panes -->
						<div class="my_tab_content">
							<div class="my_fade my_tab" id="create_user">
								<div class="widget-body">

                                                                    <form method="POST" action="" id="create_single" url="create_account.php?step=create_user" name="create_single_user">
                                                                            <div class="col-md-6">
                                                                                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CREATE_BASIC_INFO"); ?></h4></br>

                                                                                <div class="form-group m-b-sm">
                                                                                    <label for="firstname_input"><?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?></label>
                                                                                    <input type="text" id="firstname_input" name="firstname" placeholder="<?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?>" class="form-control">
                                                                                </div>

                                                                                <div class="form-group m-b-sm">
                                                                                    <label for="surname_input"><?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?></label>
                                                                                    <input type="text" id="surname_input" name="surname" placeholder="<?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?>" class="form-control">
                                                                                </div>

                                                                                <div class="form-group m-b-sm">
                                                                                    <label for="email_input"><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></label>
                                                                                    <input type="text" id="email_input" name="email" placeholder="<?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>" class="form-control">
                                                                                </div>

                                                                                <div class="form-group m-b-sm">
                                                                                    <label for="password_input"><?php echo TranslationHandler::get_static_text("PASSWORD")  . " " . TranslationHandler::get_static_text("OPTIONAL"); ?></label>
                                                                                    <input type="password" id="password_input" name="password" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD") ; ?>" class="form-control">
                                                                                </div>
                                                                            </div>


                                                                            <div class="col-md-6">
                                                                                <div class="form-group m-b-sm">
                                                                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CREATE_AFFILIATIONS"); ?></h4></br>

                                                                                    <label for="user_type"><?php echo TranslationHandler::get_static_text("CREATE_USERTYPE"); ?></label>

                                                                                        <?php
                                                                                            if(RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN"))
                                                                                            {
                                                                                                echo '<select id="user_type" name ="usertype" class="create_select_usertype form-control">';
                                                                                                echo '<option value="SA">' . TranslationHandler::get_static_text("SUPER_ADMIN") . '</option>';
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                echo '<select id="user_type" name ="usertype" class="create_select_usertype_no_school form-control">';
                                                                                            }

                                                                                            if(RightsHandler::has_user_right("ACCOUNT_CREATE_LOCADMIN"))
                                                                                            {
                                                                                                echo '<option value="A">' . TranslationHandler::get_static_text("ADMIN") . '</option>';
                                                                                            }

                                                                                        ?>
                                                                                        <option value="T"><?php echo TranslationHandler::get_static_text("TEACHER"); ?></option>'
                                                                                        <option value="S"><?php echo TranslationHandler::get_static_text("STUDENT"); ?></option>'
                                                                                    </select>
                                                                                </div>

                                                                                <div class="form-group m-b-sm">
                                                                                    <div class="create_select_school <?php echo (RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN") ? '" style="visibility:hidden;' : ''); ?> ">
                                                                                        <?php
                                                                                        if(RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN"))
                                                                                        {?>
                                                                                            <label for="select1" class="control-label"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_SCHOOL");?></label>
                                                                                            <select id="select1" name="school_id" class="create_select_school form-control" data-plugin="select2">
                                                                                                <option value=""><?php echo TranslationHandler::get_static_text("CREATE_SELECT_SCHOOL"); ?></option>
                                                                                                <?php

                                                                                                    $get_id = (isset($_GET['school_id']) && is_numeric($_GET['school_id'])) ? $_GET['school_id'] : "";

                                                                                                    $schoolHandler->get_all_schools(true);
                                                                                                    foreach($schoolHandler->all_schools as $school)
                                                                                                    {
                                                                                                        $insert = $school->id = $get_id ? " selected " : "";
                                                                                                        echo '<option ' . $insert . 'value = "' . $school->id . '">' . $school->name . ', ' . $school->address . '</option>';
                                                                                                    }

                                                                                                ?>
                                                                                            </select>
                                                                                        <?php
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $get_id = (isset($_GET['school_id']) && is_numeric($_GET['school_id'])) ? $_GET['school_id'] : "";
                                                                                            echo '<input type="hidden" name="school_id" value="' . $get_id .'">';
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group m-b-sm">
                                                                                    <div class="create_select_class <?php echo (RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN") ? '' : '" style="visibility:hidden;'); ?>">
                                                                                        <label for="select_class_name"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_CLASS") . " " . TranslationHandler::get_static_text("OPTIONAL"); ?></label>
                                                                                        <select id="select_class_name" name="class_name[]" class="form-control" data-plugin="select2" multiple>
                                                                                            <?php

                                                                                                $class_ids = (isset($_GET['class_ids']) && is_array($_GET['class_ids'])) ? $_GET['class_ids'] : array();

                                                                                                $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
                                                                                                foreach($classHandler->classes as $class)
                                                                                                {
                                                                                                    $insert = in_array($class->id, $class_ids) ? " selected " : "";
                                                                                                    echo '<option' . $insert . 'value = "' . $class->id . '">' . $class->title . '</option>';
                                                                                                }
                                                                                            ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                        </div>

                                                                        <div style="clear:both;"></div>

                                                                        <div class="form-group">
                                                                            <div class="col-md-12">

                                                                                <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("CREATE_SUBMIT"); ?>" class="pull-left btn btn-default btn-sm create_submit_info">
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                        </div><!-- .tab-pane  -->




                                                        <div class="my_fade my_tab" id="import_users">
                                                            <div class="widget-body">

                                                                    <form method="POST" action="" id="create_import_form" url="create_account.php?step=import_users" name="create_import" enctype="multipart/form-data">

                                                                            <?php if(RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN"))
                                                                                {
                                                                                ?>
                                                                                    <div class="import_select_school form-group m-b-md">


                                                                                        <label for="select1"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_SCHOOL"); ?></label>
                                                                                        <select id="select1" name="school_id" class="import_select_school form-control" data-plugin="select2">
                                                                                            <option value=""><?php echo TranslationHandler::get_static_text("CREATE_SELECT_SCHOOL"); ?></option>
                                                                                            <?php
                                                                                                $schoolHandler->get_all_schools(true);
                                                                                                foreach($schoolHandler->all_schools as $school)
                                                                                                {
                                                                                                    echo '<option value = "' . $school->id . '">' . $school->name . ', ' . $school->address . '</option>';
                                                                                                }
                                                                                            ?>

                                                                                        </select>

                                                                                    </div>

                                                                                    <div class="form-group m-b-md">
                                                                                        <div class="import_select_class <?php echo (RightsHandler::has_user_right("ACCOUNT_CREATE_SYSADMIN") ? '" style="visibility:hidden;height:0px;"' : '"'); ?> >
                                                                                            <label for="import_class_name"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_CLASS") . " " . TranslationHandler::get_static_text("OPTIONAL"); ?></label>
                                                                                            <select id="import_class_name" name="class_name[]" class="import_select_class form-control" data-plugin="select2" multiple>
                                                                                                <?php
                                                                                                if($userHandler->_user->user_type_id > 1)
                                                                                                {
                                                                                                    $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
                                                                                                    foreach($classHandler->classes as $class)
                                                                                                    {
                                                                                                        echo '<option value = "' . $class->id . '">' . $class->title . '</option>';
                                                                                                    }
                                                                                                }
                                                                                            ?>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                <?php
                                                                                }
                                                                                else
                                                                                {
                                                                                ?>

                                                                                    <div class="form-group m-b-md">

                                                                                        <label for="import_class_name"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_CLASS") . " " . TranslationHandler::get_static_text("OPTIONAL"); ?></label>
                                                                                        <select id="import_class_name" name="class_name[]" class="import_select_class form-control" data-plugin="select2" multiple>
                                                                                            <?php

                                                                                            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
                                                                                            foreach($classHandler->classes as $class)
                                                                                            {
                                                                                                echo '<option value = "' . $class->id . '">' . $class->title . '</option>';
                                                                                            }

                                                                                        ?>
                                                                                        </select>

                                                                                    </div>

                                                                                <?php
                                                                                }
                                                                                ?>

                                                                                <div class="form-group m-b-sm">
                                                                                    <label for="csv_file_dialog"><?php echo TranslationHandler::get_static_text("CREATE_SELECT_FILE"); ?></label>
                                                                                    <input type="file" id="csv_file_dialog" name="csv_file" accept=".csv" class="form-control btn btn-default btn-sm">
                                                                                </div>

                                                                                <div class="form-group m-b-sm">
                                                                                    <input type="button" id="create_import_submit" name="submit" value="<?php echo TranslationHandler::get_static_text("CREATE_IMPORT"); ?>" class="btn btn-default btn-sm create_submit_csv">
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



<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>