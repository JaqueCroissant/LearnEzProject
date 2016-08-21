<?php

require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';

if(!isset($_GET['school_id']) || !isset($_GET['class_id']) || !is_numeric($_GET['school_id']) || !is_numeric($_GET['class_id']))
{
    ErrorHandler::show_error_page("DEFAULT");
}
else
{
    
    $classHandler = new ClassHandler();
    $userHandler = new UserHandler();
    
    $userHandler->get_by_class_id($_GET['class_id']);
    $students_in_class = $userHandler->users;
    
    $userHandler->get_by_school_id($_GET['school_id']);
    $students_in_school = $userHandler->users;
    
    $classHandler->get_class_by_id($_GET['class_id'])
?>
<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CLASS") . ": " . $classHandler->school_class->title; ?></h4>
                        </div>
                        <hr class="widget-separator">
            <div class="widget-body">
                <form method="POST" action="" id="add_students" url="" name="">
                    
                    <div class="col-md-5">
                        <div class="form-group m-b-sm">
                            <label for="firstname_input"><?php echo TranslationHandler::get_static_text("STUDENTS_IN_SCHOOL"); ?></label>
                            <select id="leftValues" size="5" multiple style="width: 100%; height:200px;">
                                <?php
                                    foreach($students_in_school as $student)
                                    {
                                        if(!in_array($student, $students_in_class))
                                        {
                                            echo '<option id="' . $student->id . '">' . $student->firstname . " " . $student->surname . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group m-b-sm">
                            <input type="button" id="add_class_student_btn_left" class="btn btn-default btn-sm" value="&lt;&lt;" />
                            <input type="button" id="add_class_student_btn_right" class="btn btn-default btn-sm" value="&gt;&gt;" />
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="form-group m-b-sm">
                            <label for="email_input"><?php echo TranslationHandler::get_static_text("STUDENTS_IN_CLASS"); ?></label>
                            <select id="rightValues" size="5" multiple style="width:100%; height:200px;">
                                <?php
                                    foreach($students_in_class as $student)
                                    {
                                        echo '<option id="' . $student->id . '">' . $student->firstname . " " . $student->surname . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group m-b-sm pull-right">
                            <input type="button" name="submit" id="create_single_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="btn btn-default btn-sm create_submit_info" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
<?php
}
?>

<script src="assets/js/include_app.js" type="text/javascript"></script>