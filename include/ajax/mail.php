<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/mailHandler.php';

if(isset($_POST)) {
    $current_step = isset($_GET["step"]) ? $_GET["step"] : null;
    $current_page = (isset($_POST["current_page"]) ? $_POST["current_page"] : (isset($_GET["current_folder"]) ? $_GET["current_folder"] : null));
    $mails = (isset($_POST["mail"]) ? $_POST["mail"] : (isset($_GET["mail_id"]) ? array($_GET["mail_id"]) : array()));
    
    $mailHandler = new MailHandler($current_page);

    switch($current_step) {
        case "search":
            if($mailHandler->search_mail(isset($_POST["search_word"]) ? $_POST["search_word"] : null, isset($_POST["search_folders"]) ? $_POST["search_folders"] : array(), isset($_POST["search_content"]) ? $_POST["search_content"] : 0)) {
                $jsonArray['status_value'] = true;
                $jsonArray['url'] = "&search_query=".$_POST["search_word"]."&search_folders=".serialize($_POST["search_folders"])."&search_content=".$_POST["search_content"];
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mailHandler->error->title;
            }
            break;
        
        case 'sent':
            if($mailHandler->assign_mail_folder($current_step, $mails)) {
                $jsonArray['status_value'] = true;
                $jsonArray['mails_removed'] = $mailHandler->mails_removed;
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mailHandler->error->title;
            }
            break;
            
        case "create_mail":
            if($mailHandler->send_mail(isset($_POST["title"]) ? $_POST["title"] : null, isset($_POST["message"]) ? $_POST["message"] : null, isset($_POST["recipiants"]) ? array($_POST["recipiants"]) : array(), isset($_POST["disable_reply"]) ? true : false, isset($_POST["mail_tags"]) ? $_POST["mail_tags"] : array())) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("MAIL_SENT");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mailHandler->error->title;
            }
            break;

        default:
            if($mailHandler->assign_mail_folder($current_step, $mails)) {
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
    