<?php
    require_once 'include/extra/required_datamodels.php';
    require_once 'include/extra/global.function.php';
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
    require_once 'include/handler/paginationHandler.php';
    require_once 'include/handler/mailHandler.php';
    require_once 'include/handler/schoolHandler.php';
    require_once 'include/handler/classHandler.php';
    
    $loginHandler = new LoginHandler();
    $rightsHandler = new RightsHandler();
    $userHandler = new UserHandler();
    $pageHandler = new PageHandler();
    $schoolHandler = new SchoolHandler();
    $classHandler = new ClassHandler();
    
    
    SessionKeyHandler::add_to_session("user_handler", $userHandler, true);
    SessionKeyHandler::add_to_session("school_handler", $schoolHandler, true);
    SessionKeyHandler::add_to_session("class_handler", $classHandler, true);
?>