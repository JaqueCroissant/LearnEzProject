<?php
<<<<<<< HEAD
    require_once 'include/extra/required_datamodels.php';
    require_once 'include/handler/errorHandler.php';
    require_once 'include/handler/dbHandler.php';
    require_once 'include/handler/sessionKeyHandler.php';
    require_once 'include/handler/rightsHandler.php';
=======
    session_start();
    require_once "include/extra/db.class.php";
    require_once 'include/handler/errorHandler.php';
    require_once 'include/handler/dbHandler.php';
    require_once 'include/handler/sessionKeyHandler.php';
    require_once 'include/handler/loginHandler.php';
>>>>>>> 3fbe7e7565457bd1fc8277c794fac4b255e4b202
    require_once 'include/pages/pagelist.php';
    
    $dbHandler = new DbHandler($db_username, $db_password);
<<<<<<< HEAD
    $rightsHandler = new RightsHandler();


    
=======
    $loginHandler = new LoginHandler($dbHandler);
>>>>>>> 3fbe7e7565457bd1fc8277c794fac4b255e4b202
?>