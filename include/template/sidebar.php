
<header class="aside-header">
    <div class="animated">
        <a id="app-brand" class="app-brand" href="javascript:void(0)" style="cursor:default;">
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
        <div class="media change_page" page="account_profile" args="&user_id=<?= $userHandler->_user->id ?>" style="cursor:pointer;">
            <div class="media-left">
                <div class="avatar avatar-md avatar-circle">
                    <a href="javascript:void(0)">
                        <img class="img-responsive current-avatar-image" alt="avatar" src="assets/images/profile_images/<?php echo profile_image_exists($userHandler->_user->profile_image); ?>">
                    </a>
                </div>
            </div>
            <div class="media-body">
                <div class="foldable">
                    <h5>
                        <a class="username" href="javascript:void(0)">
                        <?php echo strlen($userHandler->_user->firstname) > 13 ? substr($userHandler->_user->firstname, 0, 13) . ".." : $userHandler->_user->firstname; ?>
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

<div class="aside-scroll m-b-sm">
    <div class="scroll-menu">
        <div id="aside-inner-scroll">
            <ul class="aside-menu aside-left-menu"><?php
                $last_page_submenu = false;
                foreach ($pageHandler->get_menu(1) as $menu) {
                    if ($menu->display_menu) {
                        
                        $has_submenu = false;
                        foreach($menu->children as $child) {
                            if($child->display_menu) {
                                $has_submenu = true;
                                break;
                            }
                        }
                        if (empty($menu->master_page_id) || ((int)$menu->master_page_id) < 1){
                            if(!$has_submenu && $menu->is_dropdown) {
                                continue;
                            }
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
                                            <i class="zmdi-hc-fw zmdi '. $menu->icon_class .' zmdi-hc-lg"></i>
                                        </span>
                                       <span class="menu-text foldable">'. $menu->title . '</span>';
                                if ($has_submenu){
                                    echo '  <span class="menu-caret foldable">
                                                <i class="zmdi-hc-fw zmdi zmdi-hc-sm zmdi-chevron-right"></i>
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
                                    <i class="zmdi-hc-fw zmdi p-r-lg '. $menu->icon_class .' zmdi-hc-lg"></i>' . $menu->title . '</a>
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
            <hr class="m-b-0">
        </div>
    </div>
</div>

<script>
    $(function(){
        $(document).ready(function(){
            function scroll_resize(){
                var test = $(window).height() - $(".aside-user").outerHeight() - $(".aside-header").outerHeight();
                $('#aside-inner-scroll').slimScroll({
                    height: test + "px"
                });
            }
            
            scroll_resize();
            
            $(window).on("resize", scroll_resize);
        });
    });
</script>
