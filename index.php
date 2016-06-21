<!DOCTYPE html>
<?php
require_once 'include/extra/require.php';
require_once 'test.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>LearnEZ</title>
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="css/css.css" rel="stylesheet" type="text/css"/>
        <script src="js/jQuery.js" type="text/javascript"></script>
        <script src="js/scripts.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="col-xs-12 navbar-default pull-right">
            
            <?php
            include 'include/topbar.php';
            ?>

        </div>
        <div class="noPadding col-xs-12">
            <div class="noPadding collapsed col-xs-2 navbar-default" id="navBar">
                <div class="menu_header">
                    <a href="?page=front"><img class="menu_icon" src="assets/images/news.png"><div class="menu_text collapsedTitle">Front Page</div></a>
                </div>
            </div>
            <div class="col-xs-10">
                <?php
                if (isset($_GET['page'])) {
                    if (in_array($_GET['page'], $pages)) {
                        include('include/pages/' . $_GET['page'] . '.php');
                    } else {
                        include('include/pages/front.php');
                    }
                } else {
                    include('include/pages/front.php');
                }
            ?>
            </div>
        </div>
    </body>
</html>
