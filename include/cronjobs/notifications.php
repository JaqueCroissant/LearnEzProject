<?php
    require_once 'require.php';
    DbHandler::get_instance()->query("DELETE FROM user_notifications_arguments WHERE arg_id NOT IN (SELECT arg_id FROM user_notifications)");

