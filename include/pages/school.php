<?php

$lol = "create";
switch ($lol) {
case "create":
    echo '<div class="text-center col-xs-12">
                    <h1>'. $translationHandler->get_static_text("SCHOOL_CREATE_NEW") .'</h1>';
    if (isset($_GET['step']) && $_GET['step'] == "2") {
        $schoolHandler->create_school_step_one($_POST['name'], $_POST['phone'], $_POST['address'], $_POST['email'], $_POST['school_type_id']);
        echo '  <form method="POST" action="?crud=create&step=3" name="create_school">
                    <input name="max_students" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_MAX_STUDENTS") .'"><br/>
                    <input name="subscription_end" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_SUBSCRIPTION_END") .'"><br/>
                    <input type="submit" class="form-control login_submit" name="complete_step_one" value="'. $translationHandler->get_static_text("SCHOOL_FINISH_STEP_ONE") .'" style="margin-top:2px;height: 34px;font-size:14px !important;">
                </form>';
    } else {
        if (isset($_GET['step']) && $_GET['step'] == "3") {
            if ($schoolHandler->create_school_step_two($schoolHandler->school, $_POST['max_students'], $_POST['subscription_end'])) {
                echo $translationHandler->get_static_text("SCHOOL_CREATED_SUCCESSFULLY");
            } else {
                echo $schoolHandler->error->title;
            }
        }
        echo '  <form method="POST" action="?crud=create&step=2" name="create_school">
                    <input name="name" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_NAME") .'"><br/>
                    <input name="address" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_ADDRESS") .'"><br/>
                    <input name="phone" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_PHONE") .'"><br/>
                    <input name="email" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_EMAIL") .'"><br/>
                    <input name="school_type_id" type="text" value="" class="form-control login_input" placeholder="'. $translationHandler->get_static_text("SCHOOL_TYPE_ID") .'"><br/>
                    <input type="submit" class="form-control login_submit" name="complete_step_one" value="'. $translationHandler->get_static_text("SCHOOL_FINISH_STEP_ONE") .'" style="margin-top:2px;height: 34px;font-size:14px !important;">
                </form>';
    }
    echo '</div>';
    break;
case "update":
    echo '<h1>Update school</h1>';
    break;
case "delete":
    echo '<h1>Delete school</h1>';
    break;
default:
    break;
}
?>

