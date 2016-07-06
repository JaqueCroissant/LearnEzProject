<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = SessionKeyHandler::get_from_session("school_handler", true);
$schoolHandler->get_school_types();
?>

<div class="main_container">
    <div class="input-container">
        <div id="step_one">
            <div class="material_design_header"><?php echo TranslationHandler::get_static_text("SCHOOL_CREATE_NEW"); ?></div>
            <form method="post" id="create_school_step_one" action="" url="create_school.php">
                <table style="width: 100%;">
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>
                        </td>
                        <td class="right-col">
                            <input class="material_design_input" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>
                        </td>
                        <td class="right-col">
                            <input class="material_design_input" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>
                        </td>
                        <td class="right-col">
                            <input class="material_design_input" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>
                        </td>
                        <td class="right-col">
                            <input class="material_design_input" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?>
                        </td>
                        <td class="right-col">
                            <select class="material_design_select" name="school_type_id">
                                <option value=""><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></option>
                                <?php
                                foreach ($schoolHandler->school_types as $value) {
                                    echo '<option value="'. $value['id'] .'">'. $value['title'] .'</option>';
                                }
                                ?>
                            </select>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="left-col">
                        </td>
                        <td class="right-col">
                            <input type="hidden" name="step" id="create_school_step">
                            <input type="button" name="submit" id="create_school_step_one_button" step="1" value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_ONE"); ?>" class="pull-right material_design_button create_school">   
                        </td>
                    </tr>
                </table>

            </form>
        </div>
    
        <div id="step_two" class="hidden">
            <div class="material_design_header"><?php echo TranslationHandler::get_static_text("SCHOOL_CREATE_NEW"); ?></div>
            <form method="POST" id="create_school_step_two" action="" url="create_school.php">
                <table style="width:100%">
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>
                        </td>
                        <td class="right-col">
                            <input class="material_design_input" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>
                        </td>
                        <td class="right-col">
                            <input name="school_subscription_end" class="material_design_input" type="text" id="datepicker">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                        </td>
                        <td class="right-col">
                            <input type="hidden" name="step" id="create_school_step_2">
                            <input type="button" name="submit" id="create_school_step_two_button" step="2" value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_TWO"); ?>" class="pull-right material_design_button create_school">   
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    
        <div id="step_three" class="hidden">
            
        </div>
    </div>
    <div class="input-container">
        <div class="progress">
            <div class="progress-bar-success progress-bar-striped"><span>33% completed</span></div>
        </div>
    </div>
</div>




<?php
//$lol = "create";
//switch ($lol) {
//case "create":
//    echo '<div class="col-xs-12">
//                    <h1>'. TranslationHandler::get_static_text("SCHOOL_CREATE_NEW") .'</h1>';
//    if (isset($_GET['step']) && $_GET['step'] == "2") {
//        $schoolHandler->create_school_step_one($_POST['name'], $_POST['phone'], $_POST['address'], $_POST['email'], $_POST['school_type_id']);
//        echo '  <form method="POST" action="?crud=create&step=3" name="create_school">
//                    <input name="max_students" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS") .'"><br/>
//                    <input name="subscription_end" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END") .'"><br/>
//                    <input type="submit" class="form-control login_submit" name="complete_step_one" value="'. TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_ONE") .'" style="margin-top:2px;height: 34px;font-size:14px !important;">
//                </form>';
//    } else {
//        if (isset($_GET['step']) && $_GET['step'] == "3") {
//            if ($schoolHandler->create_school_step_two($schoolHandler->school, $_POST['max_students'], $_POST['subscription_end'])) {
//                echo TranslationHandler::get_static_text("SCHOOL_CREATED_SUCCESSFULLY");
//            } else {
//                echo $schoolHandler->error->title;
//            }
//        }
//        echo '  <form method="POST" action="?crud=create&step=2" name="create_school">
//                    <input name="name" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_NAME") .'"><br/>
//                    <input name="address" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_ADDRESS") .'"><br/>
//                    <input name="phone" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_PHONE") .'"><br/>
//                    <input name="email" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_EMAIL") .'"><br/>
//                    <input name="school_type_id" type="text" value="" class="form-control login_input" placeholder="'. TranslationHandler::get_static_text("SCHOOL_TYPE_ID") .'"><br/>
//                    <input type="submit" class="form-control login_submit" name="complete_step_one" value="'. TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_ONE") .'" style="margin-top:2px;height: 34px;font-size:14px !important;">
//                </form>';
//    }
//    echo '</div>';
//    break;
//case "update":
//    echo '<h1>Update school</h1>';
//    break;
//case "delete":
//    echo '<h1>Delete school</h1>';
//    break;
//default:
//    break;
//}
//?>