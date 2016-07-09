<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = new PageHandler();

$jsonArray['status_value'] = true;
if(PageHandler::page_exists(isset($_GET['page']) ? $_GET['page'] : "front")) {
    $jsonArray['page_arguments'] = isset($_GET['step']) ? $_GET['step'] : null;
    if($pageHandler->get_page_from_name($_GET['page'], $jsonArray['page_arguments'])) {
        $jsonArray['pagename'] = $_GET['page'];
        $breadcrumbs = $pageHandler->get_breadcrumbs_array();
    } else {
        $jsonArray['pagename'] = "error";
        $breadcrumbs = $pageHandler->generate_page("error");
    }
    $jsonArray['breadcrumbs'] = $pageHandler->generate_breadcrumbs($breadcrumbs);

} else {
    $jsonArray['pagename'] = "error";
    $lol = $pageHandler->generate_page("error");
    $jsonArray['breadcrumbs'] = $pageHandler->generate_breadcrumbs($lol);
}

echo json_encode($jsonArray);

