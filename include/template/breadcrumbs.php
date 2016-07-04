<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = SessionKeyHandler::get_from_session("page_handler", true);

$breadcrumbs = $pageHandler->get_breadcrumbs_array();
for($i = 0; $i < count($breadcrumbs); $i++) {
    if($breadcrumbs[$i]->is_dropdown || $i+1 >= count($breadcrumbs)) {
        echo $breadcrumbs[$i]->title;    
    } else {
        echo '<a class="change_page" ';
        echo ' page="'. $breadcrumbs[$i]->pagename .'" args="'. $breadcrumbs[$i]->page_arguments . '"';
        echo ' id="'.$breadcrumbs[$i]->pagename.'" href="#">'. $breadcrumbs[$i]->title .' </a>'; 
    }
}