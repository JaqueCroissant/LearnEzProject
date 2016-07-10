<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/mailHandler.php';
$current_step = isset($_GET["step"]) ? $_GET["step"] : null;
if(isset($_POST)) {
    $mailHandler = new MailHandler(isset($_POST["current_page"]) ? $_POST["current_page"] : null);
    
    switch($current_step) {
        case 'sent':
            if($mailHandler->assign_mail_folder($current_step, $_POST["mail"])) {
                $jsonArray['status_value'] = true;
                $jsonArray['mails_removed'] = $mailHandler->mails_removed;
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mailHandler->error->title;
            }
            break;
        
        default:
            if($mailHandler->assign_mail_folder($current_step, $_POST["mail"])) {
                $jsonArray['status_value'] = true;
                $jsonArray['mails_removed'] = $mailHandler->mails_removed;
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mailHandler->error->title;
            }
            break;
    }
    echo json_encode($jsonArray);
}
    