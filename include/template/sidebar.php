<?php

foreach($pageHandler->get_menu(1) as $menu) {
    if($menu->display_menu) {
        echo '<a ';
        echo (!$menu->is_dropdown) ? 'class="change_page"' : '';
        echo ' page="'. $menu->pagename .'" args="'. $menu->page_arguments . '"';
        echo ' id="'.$menu->pagename.'" href="#">
            <div class="menu_header">
                <img class="menu_icon" src="assets/images/'. $menu->image .'">
                <div class="menu_text">'. $menu->title .'</div>
            </div>
        </a>';
    }
}
