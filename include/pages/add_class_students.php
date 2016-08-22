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
                <form method="POST" action="" id="add_students" url="add_class_students.php" name="add_students_form">
                    <input type="hidden" name="class" value="<?= $_GET['class_id']?>">
                    <input type="hidden" name="school" value="<?= $_GET['school_id']?>">
                    <div class="col-md-5">
                        <div class="form-group m-b-sm">
                            <label for="firstname_input"><?php echo TranslationHandler::get_static_text("STUDENTS_IN_SCHOOL"); ?></label>
                            <select name="students_to_remove[]" id="leftValues" size="5" multiple style="width: 100%; height:200px;" class="form-control">
                                <?php
                                    foreach($students_in_school as $student)
                                    {
                                        $is_present = false;

                                        foreach($students_in_class as $value)
                                        {
                                            if($student->id == $value->id)
                                            {
                                                $is_present = true;
                                            }
                                        }

                                        if(!$is_present)
                                        {
                                            echo '<option value="' . $student->id . '">' . $student->firstname . " " . $student->surname . " - " . $student->username . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group m-b-sm">
                            <input type="button" id="add_class_student_btn_left" class="btn btn-default btn-lg bnf_btn" value="&lt;&lt;" style="float:left;"/>
                            <input type="button" id="add_class_student_btn_right" class="btn btn-default btn-lg bnf_btn" value="&gt;&gt;" style="float:right;" />
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="form-group m-b-sm">
                            <label for="email_input"><?php echo TranslationHandler::get_static_text("STUDENTS_IN_CLASS"); ?></label>
                            <select name="students_to_add[]" id="rightValues" size="5" multiple style="width:100%; height:200px;" class="form-control">
                                <?php
                                    foreach($students_in_class as $student)
                                    {
                                        echo '<option value="' . $student->id . '">' . $student->firstname . " " . $student->surname . " - " . $student->username . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group m-b-sm pull-right">
                            <input type="button" name="submit" id="class_students_submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="btn btn-default add_students_submit" >
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