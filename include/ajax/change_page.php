<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = new PageHandler();

$jsonArray['status_value'] = true;
if(PageHandler::page_exists(isset($_GET['page']) ? $_GET['page'] : "front")) {
    $jsonArray['step'] = isset($_GET['step']) ? $_GET['step'] : null;
    $args_string = generate_args_string($_GET);
    if($pageHandler->get_page_from_name($_GET['page'], $jsonArray['step'], $args_string)) {
        $jsonArray['pagename'] = $_GET['page'];
        $breadcrumbs = $pageHandler->get_breadcrumbs_array();
    } else {
        $jsonArray['pagename'] = "error";
        $breadcrumbs = $pageHandler->generate_page("error");
    }
    $jsonArray['breadcrumbs'] = $pageHandler->generate_breadcrumbs($breadcrumbs);

} else {
    $jsonArray['pagename'] = "error";
    $jsonArray['breadcrumbs'] = $pageHandler->generate_breadcrumbs($pageHandler->generate_page("error"));
}

function generate_args_string($array = array()) {
    unset($array["step"]); unset($array["page"]);
    $args_string = "";
    foreach($array as $key => $value) {
        $args_string .= "&" . $key . "=" . $value;
    }
    return $args_string;
}

echo json_encode($jsonArray);

