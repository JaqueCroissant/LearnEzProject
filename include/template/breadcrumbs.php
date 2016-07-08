<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = new pageHandler();

if($pageHandler->get_page_from_name($_POST['pagename'])) {
    $breadcrumbs = $pageHandler->get_breadcrumbs_array();
    for($i = 0; $i < count($breadcrumbs); $i++) {
        if($breadcrumbs[$i]->is_dropdown || $i+1 >= count($breadcrumbs)) {
            echo '<span class="">' . $breadcrumbs[$i]->title . '</span>';    
        } else {
            echo '<a class="change_page text-white fw-600" ';
            echo ' page="'. $breadcrumbs[$i]->pagename .'" args="'. $breadcrumbs[$i]->page_arguments . '"';
            echo ' id="'.$breadcrumbs[$i]->pagename.'" href="#">'. $breadcrumbs[$i]->title .' </a>'; 
        }
        if ($i <count($breadcrumbs)-1) {
            echo '<span class="material_font">
                <span class="zmdi-chevron-right p-v-xs"></span>
            </span>';
        } 
    }
}