<?php
session_start();
require_once '../../include/handler/handler.php';
require_once '../../include/handler/dbHandler.php';
require_once '../../include/handler/sessionKeyHandler.php';
require_once '../../include/handler/errorHandler.php';
require_once '../../include/handler/translationHandler.php';


require_once '../../include/extra/required_datamodels.php';




if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    die("Direct access not permitted");
}