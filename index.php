<!DOCTYPE html>
<?php
require_once 'include/extra/require.php';

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>LearnEZ</title>
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="css/css.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="col-xs-12 navbar-default pull-right">
            
            <?php
            include 'include/topbar.php';
            ?>

        </div>
        <div class="col-xs-12">
            <div class="col-xs-1 navbar-default">
                <ul>
                    <a href="?page=front"><li>Front Page</li></a>
                    <a href='?page=news'><li>News</li></a>
                    <a href="?page=courses"><li>Courses</li></a>


                </ul>

            </div>
            <div class="col-xs-11">
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
