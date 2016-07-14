<!--<a href="#" class="change_page" page="front" id="front">
    <div class="pull-left title">
        <img src="assets/images/LearnEZ-Maskot-sort-30-30.png" class="title_icon">
        <div class="headline">Learn<span id="EZ">EZ</span></div>
    </div>
</a>-->
<?php 

?>

<header class="aside-header">
    <div class="animated">
        <a id="app-brand" class="app-brand change_page" page="front" href="javascript:void(0)">
            <span id="brand-icon" class="brand-icon">
                <img src="assets/images/LearnEZ-Maskot-sort-30-30.png" class="fa fa-gg">
            </span>
            <span id="brand-name" class="brand-icon foldable">Learn EZ</span>
        </a>
    </div>
</header>

<?php
    if (SessionKeyHandler::session_exists("user")) {
?>
    <div class="aside-user">
        <div class="media">
            <div class="media-left">
                <div class="avatar avatar-md avatar-circle">
                    <a href="javascript:void(0)">
                        <img class="img-responsive" alt="avatar" src="assets/images/221.jpg">
                    </a>
                </div>
            </div>
            <div class="media-body">
                <div class="foldable">
                    <h5>
                        <a class="username" href="javascript:void(0)">
                        <?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname; ?>
                        </a>
                    </h5>
                    
                    <small><?php echo $userHandler->_user->user_type_title; ?></small>
                </div>
            </div>
        </div>
    </div>
<?php
    }
?>

<div class="aside-scroll">
    <div class="slimScrollDiv">
        <div id="aside-scroll-inner" class="aside-scroll-inner">
            <ul class="aside-menu aside-left-menu"><?php
                $last_page_submenu = false;
                foreach ($pageHandler->get_menu(1) as $menu) {
                    if ($menu->display_menu) {
                        $has_submenu = (count($menu->children) > 0) ? true : false;
                        if (empty($menu->master_page_id) || ((int)$menu->master_page_id) < 1){
                            if($last_page_submenu) {
                                echo '</ul>';
                                $last_page_submenu = false;
                            }

                                echo '<li class="menu-item ';
                                if ($has_submenu) {
                                    echo 'has-submenu';
                                }
                                echo '"> <a class="menu-link ';
                                if ($has_submenu) {
                                    echo 'submenu-toggle';
                                } else {
                                    echo 'change_page';
                                }
                                echo '" ';
                                if(!$menu->is_dropdown) {
                                    echo '
                                        page="'. $menu->pagename .'" step="'. $menu->step . '"
                                        id="'.$menu->pagename.'"';
                                }
                                echo ' href="javascript:void(0)">';
                                echo '  <span class="menu-icon">
                                            <i class="zmdi '. $menu->icon_class .' zmdi-hc-lg"></i>
                                        </span>
                                       <span class="menu-text foldable">'. $menu->title . '</span>';
                                if ($has_submenu){
                                    echo '  <span class="menu-caret foldable">
                                                <i class="zmdi zmdi-hc-sm zmdi-chevron-right"></i>
                                            </span>';
                                }
                                echo '</a>';
                                if ($has_submenu) {
                                    echo '<ul class="submenu" style="display: none;">';
                                }


                        } else {
                            echo '  <li>
                                        <a class="change_page" 
                                         page="'. $menu->pagename .'" step="'. $menu->step . '"'
                                    . ' id="'.$menu->pagename.'" href="javascript:void(0)">
                                    <i class="zmdi p-r-lg '. $menu->icon_class .' zmdi-hc-lg"></i>' . $menu->title . '</a>
                                    </li>';
                            $last_page_submenu = true;
                        }
                    }
                }
                if($last_page_submenu) {
                    echo '</ul>';
                    $last_page_submenu = false;
                }
                ?></ul>
            <hr>
        </div>
        <div class="slimScrollBar"></div>
        <div class="slimScrollRail"></div>
    </div>
</div>
