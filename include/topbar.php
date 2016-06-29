<a href="/LearnEZ/">
    <div class="pull-left title">
        <img src="assets/images/LearnEZ-Maskot-hvid.png" class="title_icon">
        <div class="headline">Learn<span id="EZ">EZ</span></div>
    </div>
</a>

<div class="pull-right col-xl-3 hidden-xs" style="height: 100%; margin-right: 0.5em;">
    <?php
        //var_dump($pageHandler->get_menu(2));
        foreach($pageHandler->get_menu(2) as $menu) {
            if($menu->display_menu) {
                echo '<a href="';
                echo $menu->is_dropdown ? "#" : "?page=". $menu->pagename ."". $menu->page_arguments;
                echo '" id="'.$menu->pagename.'">
                        <div class="menu_header" style="display:inline-block">
                            <div class="menu_text">
                                <img src="assets/images/'. $menu->image .'" class="menu_icon"> ';
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