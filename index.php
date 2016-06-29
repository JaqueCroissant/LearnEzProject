<?php
session_start();
require_once 'include/extra/require.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>LearnEZ</title>
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="css/css.css" rel="stylesheet" type="text/css"/>
        <script src="js/jQuery.js" type="text/javascript"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="js/scripts.js" type="text/javascript"></script>
        <?php        
        if (SessionKeyHandler::session_exists("user")) {
            ?>
            <script src="js/backgroundScripts.js" type="text/javascript"></script>
            <script src="js/userGlobal.js" type="text/javascript"></script>
        <?php
        }
        ?>
    </head>
    <body>
        <div class="sidebarButton hidden-lg menu_design" id="sidebarButton">
            <a href="#">
                <img src="assets/images/ic_keyboard_arrow_right_white_18dp/web/ic_keyboard_arrow_right_white_18dp_1x.png"><br/>
                <img src="assets/images/ic_keyboard_arrow_right_white_18dp/web/ic_keyboard_arrow_right_white_18dp_1x.png"><br/>
                <img src="assets/images/ic_keyboard_arrow_right_white_18dp/web/ic_keyboard_arrow_right_white_18dp_1x.png">
            </a>
        </div>
        <div class="noPadding topbar menu_design">
            <?php
            include 'include/topbar.php';
            ?>
        </div>      
        <div class="noPadding sidebar menu_design collapsed hidden-md hidden-sm hidden-xs" id="navBar">
            <?php 
            include 'include/sidebar.php';
            ?>
        </div>       
        <div class='col-md-12 content'>
            <?php
            if($pageHandler->get_page_from_name($_GET['page'])) {
                include('include/pages/' . $pageHandler->current_page->pagename . '.php');
            } else {
                include('include/pages/front.php');
            }

        ?>
        </div>
        <div id="notificationWindow">
            
        </div>
    </body>
</html>
