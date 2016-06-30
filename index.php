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
        <script src="js/global.js" type="text/javascript"></script>
        <script src="js/navigation.js" type="text/javascript"></script>
        <script src="js/form.js" type="text/javascript"></script>
        
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
            <div id="loading_page" class="hidden">
                <img src="assets/images/loading_page.GIF" />
            </div>
            
            <div id="content_container">
            </div>
            
        </div>
        <div id="notificationWindow">
            
        </div>
    </body>
</html>
