<?php
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
    require_once 'include/handler/schoolHandler.php';
    
    $loginHandler = new LoginHandler();
    $rightsHandler = new RightsHandler();
    $userHandler = new UserHandler();
    $pageHandler = new PageHandler();
    $schoolHandler = new SchoolHandler();
    $translationHandler = new TranslationHandler();
    
    SessionKeyHandler::add_to_session("page_handler", $pageHandler, true);
    SessionKeyHandler::add_to_session("login_handler", $loginHandler, true);
    SessionKeyHandler::add_to_session("rights_handler", $rightsHandler, true);
?>