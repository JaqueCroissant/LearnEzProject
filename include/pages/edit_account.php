<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';

$classHandler = new ClassHandler();
$schoolHandler = new SchoolHandler();
$userHandler = new UserHandler();
$schoolHandler->get_all_schools();
$schoolHandler->get_school_types();
?>

<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            
            <?php
                if (RightsHandler::has_user_right("ACCOUNT_EDIT_OTHER") && isset($_GET['user_id'])) {
                    $userHandler->get_user_by_id($_GET['user_id']);
                ?>
                        <div class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("EDITING_ACCOUNT") . " " . $userHandler->temp_user->username; ?></h4>
                        </div>
                        <hr class="widget-separator">



                         <div class="widget-body">
                 
                            <form method="POST" action="" id="create_single" url="create_account.php?step=create_user" name="create_single_user">
                                <div class="col-md-6">
                                    
                                    <div class="col-md-12">
                                        <div class="form-group m-b-sm">
                                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?></label>
                                            <div class="">
                                                <input class="form-control" id="edit_first_name" type="text" name="first_name" placeholder="<?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?>" value="<?php echo $userHandler->temp_user->firstname; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group m-b-sm">
                                            <label class="control-label" for="surname"><?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?></label>
                                            <div class="">
                                                <input class="form-control" id="edit_surname" type="text" name="surname" placeholder="<?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?>" value="<?php echo $userHandler->temp_user->surname; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group m-b-sm">
                                            <label class="control-label" for="email"><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></label>
                                            <div class="">
                                                <input class="form-control" id="edit_email" type="text" name="email" placeholder="<?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>" value="<?php echo $userHandler->temp_user->email; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group m-b-sm">
                                            <label class="control-label" for="textarea1"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></label>
                                            <div class="">
                                                <textarea class="form-control" id="edit_description" name="description"><?php echo $userHandler->temp_user->description; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        
                                        
                                        <div class="form-group m-b-sm">
                                            <label class="control-label" for="password"><?php echo TranslationHandler::get_static_text("PASSWORD"); ?></label>
                                            <div style="display: table-cell;width: 100%;">
                                                <input readonly="" class="form-control" id="edit_email" type="text" name="email" value="********">
                                                
                                            </div>
                                            <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                                <input type="button" name="generate_password" id="generate_password_submit" value="<?php echo TranslationHandler::get_static_text("GEN_PASSWORD"); ?>" class="m-l-sm pull-right btn btn-default">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label class="control-label" for="class_name"><?php echo TranslationHandler::get_static_text("CLASS_NAME"); ?></label>
                                            <div class="">
                                                <select name="class_name[]" id="edit_class_name" class="form-control" data-plugin="select2" multiple>
                                                    <?php
                                                        $classHandler->get_classes_by_school_id($userHandler->temp_user->school_id);
                                                        $possible_classes = $classHandler->classes;
                                                        $classHandler->get_classes_by_user_id($userHandler->temp_user->id);
                                                        $user_classes = $classHandler->classes;

                                                        foreach ($possible_classes as $class) 
                                                        {
                                                            $insert = in_array($class, $user_classes) ? " selected " : ""; 
                                                            echo '<option ' . $insert . 'value = "' . $class->id . '">' . $class->title . '</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="clear:both;"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("CREATE_SUBMIT"); ?>" class="pull-right btn btn-default create_submit_info">
                                    </div>
                                </div>
                            </form>
                           
                        </div>
            
            
            
            
            
            
            
            
            
            
            
             <?php
            } else {
                echo ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title;
            }
            ?>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
