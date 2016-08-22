<div class="navbar-header pull-left">
    <button class="hamburger visible-lg-inline-block hamburger--arrowalt is-active js-hamburger p-r-md fz-xl go_back" type="button">
        <span class="material_font">
            <span class="zmdi-chevron-left"></span>
        </span>
    </button>
    <button id="aside-toggle" class="p-r-md hamburger hidden-lg hamburger--spin js-hamburger" type="button">
        <span class="hamburger-box">
            <span class="hamburger-inner"></span>
        </span>
    </button>
</div>

<div class="navbar-header text-white pull-left">
    <div id="content_breadcrumbs" class=""></div>
</div>
<div>
    <ul id="top-nav" class="pull-right text-white">
    <?php
    $last_page_submenu = false;
    foreach ($pageHandler->get_menu(2) as $menu) {
        if ($menu->display_menu) {
            if(!empty($menu->master_page_id) && ((int)$menu->master_page_id) > 0) {
                echo '
                    <li>
                        <a class="change_page text-black" page="'. $menu->pagename .'" step="' . $menu->step . '" id="' . $menu->pagename . '"  href="javascript:void(0)">
                            <i class="zmdi m-r-md zmdi-hc-lg '. $menu->icon_class .'"></i>
                            '. $menu->title .'
                        </a>
                    </li>';
                $last_page_submenu = true;
            } else {
                if($last_page_submenu) {
                    echo '</ul>';
                    $last_page_submenu = false;
                }
                
                echo '<li name="' . $menu->title . '" class="nav-item text-black '; 

                if($menu->is_dropdown) {
                    echo 'dropdown';
                } 
                echo '">';

                if($menu->is_dropdown) {
                    echo '
                    <a class="dropdown-toggle" aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" data-tooltip="tooltip" data-placement="bottom" data-trigger="hover" title="'. $menu->title .'" href="javascript:void(0)">
                        <i class="zmdi zmdi-hc-lg '. $menu->icon_class .'"></i>
                    </a>
                    <ul class="dropdown-menu animated flipInY">';
                } else {
                    echo $menu->pagename == "notifications" ? '<div id="notification_counter" class="hidden"></div>' : '';
                    echo '<a class="';
                    if($menu->pagename == "login") {
                        echo "display_login_overlay";
                    }elseif($menu->pagename == "logout") {
                        echo "log_out";
                    } elseif($menu->pagename != "notifications") {
                        echo "change_page";
                    }
                    echo '" page="'. $menu->pagename .'" step="' . $menu->step . '" id="' . $menu->pagename . '" data-tooltip="tooltip" data-placement="bottom" data-trigger="hover" title="'. $menu->title .'" href="javascript:void(0)">
                        <i class="zmdi zmdi-hc-lg '. $menu->icon_class .'"></i>
                    </a>';
                }
                echo '</li>';
            }
        }
    }
    if($last_page_submenu) {
        echo '</ul>';
        $last_page_submenu = false;
    }
    ?>
        
    </ul>
</div>
<script>
$(document).ready(function(){
    $('[data-tooltip="tooltip"]').tooltip(); 
});
</script>