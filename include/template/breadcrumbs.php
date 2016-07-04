<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = SessionKeyHandler::get_from_session("page_handler", true);

echo $pageHandler->get_breadcrumbs_array($pageHandler->current_page);

$pageHandler->get_page_from_name($pageHandler->current_page->pagename);
