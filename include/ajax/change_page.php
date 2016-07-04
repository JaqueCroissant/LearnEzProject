<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = SessionKeyHandler::get_from_session("page_handler", true);

if($pageHandler->get_page_from_name(isset($_GET['page']) ? $_GET['page'] : "front")) {
    $jsonArray['status_value'] = true;
    $jsonArray['pagename'] = $pageHandler->current_page->pagename;
    SessionKeyHandler::add_to_session("page_handler", $pageHandler, true);
} else {
    $jsonArray['status_value'] = false;
}

echo json_encode($jsonArray);

