<?php
session_start();
//Database handlers
require_once '../include/extra/db.class.php';
require_once '../include/handler/dbHandler.php';
//Session handlers
require_once '../include/handler/sessionKeyHandler.php';
//Other handlers
require_once '../include/handler/notificationHandler.php';
require_once '../include/handler/translationHandler.php';
require_once '../include/handler/errorHandler.php';
//Datamodels extensions
require_once '../include/class/orm.class.php';
//Datamodels
require_once '../include/class/user.class.php';
require_once '../include/class/notification.class.php';
require_once '../include/class/error.class.php';