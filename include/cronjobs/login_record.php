<?php
require_once 'require.php';
DbHandler::get_instance()->query("DELETE FROM login_record WHERE time < DATE_SUB(CURDATE(), interval 15 day)");
?>
