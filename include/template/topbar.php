<a href="#" class="change_page" page="front" id="front">
    <div class="pull-left title">
        <img src="assets/images/LearnEZ-Maskot-hvid.png" class="title_icon">
        <div class="headline">Learn<span id="EZ">EZ</span></div>
    </div>
</a>

<div class="pull-right col-xl-3 hidden-xs" style="height: 100%; margin-right: 0.5em;">
    <?php
        foreach($pageHandler->get_menu(2) as $menu) {
            if($menu->display_menu) {
                echo '<a ';
                if(!$menu->is_dropdown) {
                    echo ($menu->pagename == "logout") ? 'class="log_out"' : 'class="change_page"';
                }
                echo (!$menu->is_dropdown) ? 'class="change_page"' : '';
                echo ' page="'. $menu->pagename .'" args="'. $menu->page_arguments . '"';
                echo ' id="'.$menu->pagename.'" href="#">
                        <div class="menu_header" style="display:inline-block">
                            <div class="menu_text">
                                <img src="assets/images/'. $menu->icon_class .'" class="menu_icon"> ';
                                if($menu->pagename == "notifications"){
                                    echo "<div id='notification_counter'></div>";
                                }
                if($menu->display_text) {
                    echo "<label style='margin-left:5px;cursor:pointer'> ". $menu->title ."</label>";
                }
                echo '
                            </div>
                        </div>
                    </a>';
            }
        }    
    ?>
</div>