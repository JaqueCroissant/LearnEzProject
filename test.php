<div class="content">
<?php
    //$dbHandler->Query("INSERT INTO `rights` (prefix, sort_order) VALUES (:prefix, :sort_order)", "HEJ", 5);
    //$dbHandler->Query("UPDATE `rights` SET prefix = :prefix, sort_order = :sort_order WHERE id = :id", "JAKOB", 10, 1);
    //echo DbHandler::getInstance()->CountQuery("SELECT * FROM `rights` WHERE prefix = :prefix AND sort_order = :sort_order", "HEJ", 5);
    //require_once 'include/handler/sessionKeyHandler.php';
    //$user = new User(array());

    echo "Current language: " . TranslationHandler::getCurrentLanguage();
    $trans = new TranslationHandler();
    echo "<br/>";
    echo "Current language: " . $trans->getCurrentLanguage(); 
    echo "<br/>";
    echo "Danish prefix: " . $trans->getStaticText("DANISH");
    $trans->setLanguage(2);
    echo "<br/>";
    echo "Current language: " . $trans->getCurrentLanguage();
    echo "<br/>";
    echo "Danish prefix: " . $trans->getStaticText("DANISH");
    $trans->setLanguage(1);
    echo "<br/>";
    echo "Current language: " . $trans->getCurrentLanguage();
    echo "<br/>";
    echo "Danish prefix: " . $trans->getStaticText("ENGLISH");
    
    echo '<h1>Mortens TEST!</h1>';
    
    
//    TEST CODE TO DELETE SCHOOL FROM ID    
//    if ($schoolHandler->delete_school_by_id(5)) {
//        echo 'success';
//    } else {
//        echo $schoolHandler->error->title;
//    }
//    
//    TEST CODE TO UPDATE SCHOOL FROM ID
//    if ($schoolHandler->update_school_by_id(1, "VUC-LOOOOL", "22334455", "testvej 1, 4000 roskilde", "test@test.dk", 1, 300, "2016/12/31")) {
//        echo 'yes';
//    } else {
//        echo $schoolHandler->error->title;
//    }
//    
//    TEST CODE TO CREATE NEW SCHOOL STEP 1
//    if ($schoolHandler->create_school_step_one("VUC", "22334455", "testvej 1, 4000 roskilde", "test@test.dk", 1)) {
//        echo 'Step one completed<br/>';
//        $lal = true;
//    } else {
//        echo $schoolHandler->error->title;
//        $lal = false;
//    }
//    
//    TEST CODE TO CREATE NEW SCHOOL STEP 2
//    if ($lal) {
//        if ($school = $schoolHandler->create_school_step_two($schoolHandler->school, 300, "2016/12/31")) {
//            echo "Step two completed and school created";
//        } else {
//            echo $schoolHandler->error->title;
//        }
//    }
//    
//    TEST CODE TO GET SCHOOL FROM ID
//    if ($schoolHandler->get_school_by_id(2)) {
//        print_r($schoolHandler->school);
//    } else {
//        echo $schoolHandler->error->title;
//    }

?>
</div>
