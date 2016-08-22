<?php
session_start();
require_once 'include/extra/require.php';

TranslationHandler::reset();
RightsHandler::reset();

?>
<!DOCTYPE html>
<html style="overflow-y: scroll !important">
    <head>
        <meta charset="UTF-8">
        <title>LearnEZ</title>
        <?php
        require_once 'include/template/head_include.php';
        ?>
    </head>
    <body class="sb-left theme-primary pace-done">
        <?php
        require_once 'include/template/login_overlay.php';
        ?>
        <div class="pace pace-inactive">
            <div class="pace-progress" style="transform: translate3d(100%, 0px, 0px);" data-progress-text="100%" data-progress="99">
                <div class="pace-progress-inner"></div>
            </div>
            <div class="pace-activity"></div>
        </div>
        <nav id="app-navbar" class="app-navbar p-l-lg p-r-md in primary">
            <?php
            include 'include/template/topbar.php';
            ?>
        </nav>      
        <aside id="app-aside" class="app-aside left in light">
            <?php
            include 'include/template/sidebar.php';
            ?>
        </aside>

        <main id="app-main" class="app-main in">
            <!--            <div id="loading_page" class="hidden">
                            <img src="assets/images/loading_page.GIF" />
                        </div>-->

            <div class="wrap">
                <section id="content_container" class="app-content">
                </section>
            </div>
        </main>

        <div id="status_container" class="hidden">
            <?php
            include 'include/template/statusbar.php';
            ?>
        </div>
        
        <?php include 'include/template/course_player.php'; ?>

        <script src="libs/bower/jQuery-Storage-API/jquery.storageapi.min.js"></script>
        <script src="libs/bower/bootstrap-sass/assets/javascripts/bootstrap.js"></script>
        <script src="libs/bower/superfish/dist/js/hoverIntent.js"></script>
        <script src="libs/bower/superfish/dist/js/superfish.js"></script>
        <script src="libs/bower/jquery-slimscroll/jquery.slimscroll.js"></script>
        <script src="libs/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
        <script src="libs/bower/PACE/pace.min.js"></script>
        <script src="assets/js/library.js"></script>
        <script src="assets/js/plugins.js"></script>
        <script src="assets/js/app.js"></script>
        <script src="libs/bower/moment/moment.js"></script>
        <script src="libs/bower/fullcalendar/dist/fullcalendar.min.js"></script>
        <script src="assets/js/fullcalendar.js"></script>
<!--        <script src="js/back.js"></script>-->
    </body>
</html>
