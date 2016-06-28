<?php

foreach($pageHandler->get_menu(1) as $menu) {
    if($menu->display_menu) {
        echo '<a href="?page='. $menu->pagename .'' . $menu->page_arguments.'">
            <div class="menu_header">
                <img class="menu_icon" src="assets/images/'. $menu->image .'">
                <div class="menu_text">'. $menu->title .'</div>
            </div>
        </a>';
    }
}
