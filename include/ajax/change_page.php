<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';

if(PageHandler::page_exists(isset($_GET['page']) ? $_GET['page'] : "front")) {
    $jsonArray['status_value'] = true;
    $jsonArray['pagename'] = $_GET['page'];
    $jsonArray['page_arguments'] = isset($_GET['step']) ? $_GET['step'] : null;
} else {
    $jsonArray['status_value'] = false;
}

echo json_encode($jsonArray);

