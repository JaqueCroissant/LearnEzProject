<?php

require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';

if(!RightsHandler::has_user_right("CLASS_ASSIGN_USER") || !isset($_GET['school_id']) || !isset($_GET['class_id']) || !is_numeric($_GET['school_id']) || !is_numeric($_GET['class_id']))
{
    ErrorHandler::show_error_page();
    die();
}
else
{
    
    $classHandler = new ClassHandler();
    $userHandler = new UserHandler();
    
    $userHandler->get_by_class_id($_GET['class_id'], false, true);
    $people_in_class = array();
    foreach ($userHandler->users as $user) {
        $user->present = true;
        $people_in_class[] = $user;
    }
    $userHandler->get_by_school_id($_GET['school_id'], true);
    $all_people = object_group_by_key(merge_array_recursively($people_in_class, $userHandler->users, true), "user_type_id");
    
    $missing = array();
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
                    <div class="col-md-12">
                        <input type="hidden" name="class" value="<?= $_GET['class_id']?>">
                        <input type="hidden" name="school" value="<?= $_GET['school_id']?>">
                        <div class="col-md-5">
                            <div class="form-group m-b-sm">
                                <label><?php echo TranslationHandler::get_static_text("STUDENTS_IN_SCHOOL"); ?></label>
                                <select size="5" multiple style="width: 100%; height:200px;" class="form-control students_left">
                                    <?php
                                        foreach($all_people["4"] as $student)
                                        {
                                            if(!isset($student->present))
                                            {
                                                echo '<option value="' . $student->id . '">' . $student->firstname . " " . $student->surname . " - " . $student->username . '</option>';
                                            }
                                            else {
                                                $missing[] = $student;
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    
                        <div class="col-md-2" style="margin-top:100px;text-align: center;">
                            <div class="btn-group">
                                <a href="javascript:void(0)" from="students_right" to="students_left" class="students_change btn btn-default"><i class="fa fa-chevron-left"></i></a>
                                <a href="javascript:void(0)" from="students_left" to="students_right" class="students_change btn btn-default"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group m-b-sm">
                                <label><?php echo TranslationHandler::get_static_text("STUDENTS_IN_CLASS"); ?></label>
                                <select name="students_to_add[]" size="5" multiple style="width:100%; height:200px;" class="form-control students_right">
                                    <?php
                                        foreach($missing as $student)
                                        {
                                            echo '<option value="' . $student->id . '">' . $student->firstname . " " . $student->surname . " - " . $student->username . '</option>';
                                        }
                                        $missing = array();
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-t-xl">
                        <div class="col-md-5">
                            <div class="form-group m-b-sm">
                                <label><?php echo TranslationHandler::get_static_text("TEACHERS_IN_SCHOOL"); ?></label>
                                <select size="5" multiple style="width: 100%; height:200px;" class="form-control teachers_left">
                                    <?php
                                        foreach($all_people["3"] as $student)
                                        {
                                            if(!isset($student->present))
                                            {
                                                echo '<option value="' . $student->id . '">' . $student->firstname . " " . $student->surname . " - " . $student->username . '</option>';
                                            }
                                            else {
                                                $missing[] = $student;
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2" style="margin-top:100px;text-align: center;">
                            <div class="btn-group">
                                <a href="javascript:void(0)" from="teachers_right" to="teachers_left" class="students_change btn btn-default"><i class="fa fa-chevron-left"></i></a>
                                <a href="javascript:void(0)" from="teachers_left" to="teachers_right" class="students_change btn btn-default"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group m-b-sm">
                                <label><?php echo TranslationHandler::get_static_text("TEACHERS_IN_CLASS"); ?></label>
                                <select name="students_to_add[]" size="5" multiple style="width:100%; height:200px;" class="form-control teachers_right">
                                    <?php
                                        foreach($missing as $student)
                                        {
                                            echo '<option value="' . $student->id . '">' . $student->firstname . " " . $student->surname . " - " . $student->username . '</option>';
                                        }
                                        $missing = array();
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group m-t-lg m-b-sm pull-right">
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