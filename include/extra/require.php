<?php
    session_start();
    require_once 'include/extra/required_datamodels.php';
    require_once 'include/handler/handler.php';
    require_once 'include/handler/errorHandler.php';
    require_once 'include/handler/dbHandler.php';
    require_once 'include/handler/sessionKeyHandler.php';
    require_once 'include/handler/pageHandler.php';
    require_once 'include/handler/rightsHandler.php';
    require_once 'include/handler/loginHandler.php';
    require_once 'include/handler/userHandler.php';
    require_once 'include/handler/translationHandler.php';
    require_once 'include/handler/notificationHandler.php';
    require_once 'include/pages/pagelist.php';

    $loginHandler = new LoginHandler();
    $rightsHandler = new RightsHandler();
    $userHandler = new UserHandler();
    $pageHandler = new PageHandler();
?>